<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CustomFieldsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomFieldsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read12:collection','write12:collection']
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
                'groups' => ['read12:collection']
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
class CustomFields
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read12:collection', 'write12:collection', 'write13:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'customFields')]
    #[Groups(['read12:collection'])]
    public ?Accounts $account = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read12:collection', 'write12:collection', 'write13:collection'])]
    public ?string $field_name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read12:collection', 'write12:collection','write13:collection'])]
    public ?string $field_type = null;

    #[ORM\Column]
    #[Groups(['read12:collection', 'write12:collection','write13:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read12:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'field', targetEntity: LandingPageFields::class)]
    #[Groups(['read:collection'])]
    public Collection $landingPageFields;

    #[ORM\OneToMany(mappedBy: 'field', targetEntity: ContactFormFields::class)]
    #[Groups(['read:collection'])]
    public Collection $contactFormFields;
    
    public function __construct()
    {
        $this->landingPageFields = new ArrayCollection();
        $this->contactFormFields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): ?Accounts
    {
        return $this->account;
    }

    public function setAccount(?Accounts $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getFieldName(): ?string
    {
        return $this->field_name;
    }

    public function setFieldName(string $field_name): self
    {
        $this->field_name = $field_name;

        return $this;
    }

    public function isFieldType(): ?string
    {
        return $this->field_type;
    }

    public function setFieldType(?string $field_type): self
    {
        $this->field_type = $field_type;

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

    /**
     * @return Collection<int, LandingPageFields>
     */
    public function getLandingPageFields(): Collection
    {
        return $this->landingPageFields;
    }

    public function addLandingPageField(LandingPageFields $landingPageField): self
    {
        if (!$this->landingPageFields->contains($landingPageField)) {
            $this->landingPageFields->add($landingPageField);
            $landingPageField->setField($this);
        }

        return $this;
    }

    public function removeLandingPageField(LandingPageFields $landingPageField): self
    {
        if ($this->landingPageFields->removeElement($landingPageField)) {
            // set the owning side to null (unless already changed)
            if ($landingPageField->getField() === $this) {
                $landingPageField->setField(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactFormFields>
     */
    public function getContactFormFields(): Collection
    {
        return $this->contactFormFields;
    }

    public function addContactFormField(ContactFormFields $contactFormField): self
    {
        if (!$this->contactFormFields->contains($contactFormField)) {
            $this->contactFormFields->add($contactFormField);
            $contactFormField->setField($this);
        }

        return $this;
    }

    public function removeContactFormField(ContactFormFields $contactFormField): self
    {
        if ($this->contactFormFields->removeElement($contactFormField)) {
            // set the owning side to null (unless already changed)
            if ($contactFormField->getField() === $this) {
                $contactFormField->setField(null);
            }
        }

        return $this;
    }
}
