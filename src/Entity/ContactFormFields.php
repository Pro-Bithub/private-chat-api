<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContactFormFieldsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactFormFieldsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['write12:collection']
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
                'groups' => ['read10:collection', 'write12:collection']
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
class ContactFormFields
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write12:collection'])]
    public ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'contactFormFields')]
    public ?ContactForms $form = null;

    #[ORM\ManyToOne(inversedBy: 'contactFormFields')]
    #[Groups(['write12:collection'])]
    public ?CustomFields $field = null;

    #[ORM\Column]
    #[Groups(['write12:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    // #[ORM\OneToMany(mappedBy: 'form_field', targetEntity: ContactCustomFields::class)]
    // private Collection $contactCustomFields;

    public function __construct()
    {
        //$this->contactCustomFields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForm(): ?ContactForms
    {
        return $this->form;
    }

    public function setForm(?ContactForms $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getField(): ?CustomFields
    {
        return $this->field;
    }

    public function setField(?CustomFields $field): self
    {
        $this->field = $field;

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

    // /**
    //  * @return Collection<int, ContactCustomFields>
    //  */
    // public function getContactCustomFields(): Collection
    // {
    //     return $this->contactCustomFields;
    // }

    // // public function addContactCustomField(ContactCustomFields $contactCustomField): self
    // // {
    // //     if (!$this->contactCustomFields->contains($contactCustomField)) {
    // //         $this->contactCustomFields->add($contactCustomField);
    // //         $contactCustomField->setFormField($this);
    // //     }

    // //     return $this;
    // // }

    // public function removeContactCustomField(ContactCustomFields $contactCustomField): self
    // {
    //     if ($this->contactCustomFields->removeElement($contactCustomField)) {
    //         // set the owning side to null (unless already changed)
    //         if ($contactCustomField->getFormField() === $this) {
    //             $contactCustomField->setFormField(null);
    //         }
    //     }

    //     return $this;
    // }
}
