<?php

namespace App\Entity;

use App\Repository\ContactOperationsRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
#[ORM\Entity(repositoryClass: ContactOperationsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'post'=> [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
            
        ],
    ],
    itemOperations: [
        'get' => [
            
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
class ContactOperations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    public ?int $contact_id = null;

    #[ORM\Column(length: 255)]
    public ?string $status = null;

    #[ORM\Column(length: 255)]
    public ?string $external_id = null;

    #[ORM\Column]
    public ?int $operation_id = null;

    #[ORM\Column(length: 1)]
    public ?string $operation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactId(): ?int
    {
        return $this->contact_id;
    }

    public function setContactId(int $contact_id): self
    {
        $this->contact_id = $contact_id;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->external_id;
    }

    public function setExternalId(string $external_id): self
    {
        $this->external_id = $external_id;

        return $this;
    }

    public function getOperationId(): ?int
    {
        return $this->operation_id;
    }

    public function setOperationId(int $operation_id): self
    {
        $this->operation_id = $operation_id;

        return $this;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): self
    {
        $this->operation = $operation;

        return $this;
    }
}
