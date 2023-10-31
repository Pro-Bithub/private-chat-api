<?php

namespace App\Entity;

use App\Repository\CustomFieldsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomFieldsRepository::class)]
class CustomFieldListValues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read12:collection', 'write12:collection', 'write13:collection'])]
    public ?int $id = null;


    #[ORM\Column(nullable: true)]
    #[Groups(['read12:collection', 'write12:collection','write13:collection'])]
    public ?string $value = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read108:collection'])]
    public ?int $custom_field_id = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomFieldId(): ?int
    {
        return $this->custom_field_id;
    }

    public function setCustomFieldId(int $custom_field_id): self
    {
        $this->custom_field_id = $custom_field_id;
        return $this;
    }


    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

  
}
