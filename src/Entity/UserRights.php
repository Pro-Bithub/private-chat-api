<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRightsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRightsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'success' => true,
                            ],
                        ],
                    ],
                ],
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
class UserRights
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userRights')]
    public ?User $user = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_gender = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_firstname = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_lastname = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_name = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_phone = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_country = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_address = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_ipaddress = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_request_category = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_request = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_origin = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_date_of_birth = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_company_name = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $contact_custom_fields = null;

    #[ORM\Column]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isContactGender(): ?string
    {
        return $this->contact_gender;
    }

    public function setContactGender(string $contact_gender): self
    {
        $this->contact_gender = $contact_gender;

        return $this;
    }

    public function isContactFirstname(): ?string
    {
        return $this->contact_firstname;
    }

    public function setContactFirstname(string $contact_firstname): self
    {
        $this->contact_firstname = $contact_firstname;

        return $this;
    }

    public function isContactLastname(): ?string
    {
        return $this->contact_lastname;
    }

    public function setContactLastname(string $contact_lastname): self
    {
        $this->contact_lastname = $contact_lastname;

        return $this;
    }

    public function isContactName(): ?string
    {
        return $this->contact_name;
    }

    public function setContactName(string $contact_name): self
    {
        $this->contact_name = $contact_name;

        return $this;
    }

    public function isContactPhone(): ?string
    {
        return $this->contact_phone;
    }

    public function setContactPhone(string $contact_phone): self
    {
        $this->contact_phone = $contact_phone;

        return $this;
    }

    public function isContactCountry(): ?string
    {
        return $this->contact_country;
    }

    public function setContactCountry(string $contact_country): self
    {
        $this->contact_country = $contact_country;

        return $this;
    }

    public function isContactAddress(): ?string
    {
        return $this->contact_address;
    }

    public function setContactAddress(string $contact_address): self
    {
        $this->contact_address = $contact_address;

        return $this;
    }

    public function isContactIpaddress(): ?string
    {
        return $this->contact_ipaddress;
    }

    public function setContactIpaddress(string $contact_ipaddress): self
    {
        $this->contact_ipaddress = $contact_ipaddress;

        return $this;
    }

    public function isContactRequestCategory(): ?string
    {
        return $this->contact_request_category;
    }

    public function setContactRequestCategory(string $contact_request_category): self
    {
        $this->contact_request_category = $contact_request_category;

        return $this;
    }

    public function isContactRequest(): ?string
    {
        return $this->contact_request;
    }

    public function setContactRequest(string $contact_request): self
    {
        $this->contact_request = $contact_request;

        return $this;
    }

    public function isContactOrigin(): ?string
    {
        return $this->contact_origin;
    }

    public function setContactOrigin(string $contact_origin): self
    {
        $this->contact_origin = $contact_origin;

        return $this;
    }

    public function isContactDateOfBirth(): ?string
    {
        return $this->contact_date_of_birth;
    }

    public function setContactDateOfBirth(string $contact_date_of_birth): self
    {
        $this->contact_date_of_birth = $contact_date_of_birth;

        return $this;
    }

    public function isContactCompanyName(): ?string
    {
        return $this->contact_company_name;
    }

    public function setContactCompanyName(string $contact_company_name): self
    {
        $this->contact_company_name = $contact_company_name;

        return $this;
    }

    public function isContactCustomFields(): ?string
    {
        return $this->contact_custom_fields;
    }

    public function setContactCustomFields(string $contact_custom_fields): self
    {
        $this->contact_custom_fields = $contact_custom_fields;

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

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }
}
