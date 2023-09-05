<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AddRegisterController;
use App\Repository\RegistrationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RegistrationsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read102:collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'post'=> [
            'method' => 'POST',
            'deserialize' => false,
            'controller' => AddRegisterController::class,
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'template' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'comment' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'url' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'status' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
                'security' => [['bearerAuth' => []]]
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read102:collection']
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
class Registrations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read102:collection'])]
    public ?int $id = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read102:collection'])]
    public ?string $accountId = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read102:collection'])]
    public ?string $name = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read102:collection'])]
    public ?string $url = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read102:collection'])]
    public ?string $slug_url = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read102:collection'])]
    public ?string $comment = null;

    #[ORM\Column]
    #[Groups(['read102:collection'])]
    public ?string $template = null;

    #[ORM\Column]
    #[Groups(['read102:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read102:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read102:collection'])]
    public ?\DateTimeInterface $date_end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlugUrl(): ?string
    {
        return $this->slug_url;
    }

    public function setSlugUrl(string $slug_url): self
    {
        $this->slug_url = $slug_url;

        return $this;
    }
    public function getAccount(): ?string
    {
        return $this->accountId;
    }

    public function setAccount(?string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function isTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function isStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(?\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }
}
