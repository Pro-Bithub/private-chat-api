<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContactCustomFieldsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactCustomFieldsRepository::class)]
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
class ContactCustomFields
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    public ?string $contactId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    public ?string $formFieldId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    public ?string $field_value = null;


    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['read:collection','read29:collection'])]
    public ?\DateTimeInterface $created_at = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?string
    {
        return $this->contactId;
    }

    public function setContact(?string $contactId): self
    {
        $this->contactId = $contactId;

        return $this;
    }

    public function getFormField(): ?string
    {
        return $this->formFieldId;
    }

    public function setFormField(?string $formFieldId): self
    {
        $this->formFieldId = $formFieldId;

        return $this;
    }

    public function getFieldValue(): ?string
    {
        return $this->field_value;
    }

    public function setFieldValue(string $field_value): self
    {
        $this->field_value = $field_value;

        return $this;
    }
}
