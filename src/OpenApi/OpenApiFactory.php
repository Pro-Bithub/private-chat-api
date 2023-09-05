<?php


namespace App\OpenApi;



use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ArrayObject;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;
    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        
        $openApi = $this->decorated->__invoke($context);
        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['bearerAuth'] = new ArrayObject([
           'type' => 'http',
           'scheme' => 'bearer',
           'bearerFormat' => 'JWT',
        ]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000',
                ],
            ],
        ]);

        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

       // $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
       // $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        //$openApi->getPaths()->addPath('/api/me', $mePathItem);

        $pathItem = new PathItem(
            post: new Operation(
            operationId: 'postApiLogin',
            tags: ['Auth'],
            responses: [
                '200' => [
                    'description' => 'Token JWT',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token',
                            ],
                        ],
                    ],
                ],
            ],
            requestBody: new RequestBody(
            content: new ArrayObject([
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/Credentials'
                    ],
                ],
            ])
        ),
        ),

        );
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        $pathItem = new PathItem(
            post: new Operation(
            operationId: 'postApiLogout',
            tags: ['Auth'],
            responses: [
            '204' => [],
        ],
        ),

        );
        $openApi->getPaths()->addPath('/logout', $pathItem);

        $pathItem = new PathItem(
            post: new Operation(
            operationId: 'postApiLoginAdmin',
            tags: ['Auth_Admin'],
            responses: [
                '200' => [
                    'description' => 'Token JWT',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token',
                            ],
                        ],
                    ],
                ],
            ],
            requestBody: new RequestBody(
            content: new ArrayObject([
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/Credentials'
                    ],
                ],
            ])
        ),
        ),

        );
       

















        return $openApi;
    }
}
