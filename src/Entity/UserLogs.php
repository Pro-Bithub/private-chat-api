<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetlogsbyaccountController;
use App\Controller\GetlogsbyuserController;
use App\Controller\GetLogsController;
use App\Repository\UserLogsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserLogsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'post'=> [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
            'normalization_context' => [
                'groups' => ['read29:collection']
            ],
        ],
        'user_logs' => [
            'method' => 'GET',
            'path' => '/getlogsbyuser/{id}',
            'deserialize' => false,
            'controller' => GetlogsbyuserController::class,
            'normalization_context' => [
                'groups' => ['read29:collection']
            ],
        ],
        'user_logs_account' => [
            'method' => 'GET',
            'path' => '/getlogsbyaccount',
            'deserialize' => false,
            'controller' => GetlogsbyaccountController::class,
            'normalization_context' => [
                'groups' => ['read29:collection']
            ],
        ],
        'user_log' => [
            'method' => 'GET',
            'path' => '/getlogbyaccount',
            'deserialize' => false,
            'controller' => GetLogsController::class,
            'normalization_context' => [
                'groups' => ['read29:collection']
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'put'=> [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'delete'=> [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
    ]    
)]
class UserLogs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection', 'read29:collection'])]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read29:collection'])]
    public ?String $user_id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $action = null;

    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $element = null;

    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?int $element_id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['read:collection','read29:collection'])]
    public ?\DateTimeInterface $log_date = null;

    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $source = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(?string $userId): self
    {
        $this->user_id = $userId;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function isElement(): ?string
    {
        return $this->element;
    }

    public function setElement(string $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getElementId(): ?int
    {
        return $this->element_id;
    }

    public function setElementId(int $element_id): self
    {
        $this->element_id = $element_id;

        return $this;
    }

    public function getLogDate(): ?\DateTimeInterface
    {
        return $this->log_date;
    }

    public function setLogDate(\DateTimeInterface $log_date): self
    {
        $this->log_date = $log_date;

        return $this;
    }

    public function isSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

   
}
