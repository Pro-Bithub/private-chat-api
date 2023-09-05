<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\ContactsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\GetContactsController;

#[ORM\Entity(repositoryClass: ContactsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'deserialize' => false,
            'controller' => GetContactsController::class,
            'normalization_context' => [
                'groups' => ['read:collection']
            ]
        ],
        'post'
        
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
        ],
        'put',
        'delete' 
    ]    
    
)]
class Contacts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection', 'read108:collection'])]
    public ?int $id = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $accountId = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $gender = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read:collection' ,'read108:collection'])]
    public ?string $firstname = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read:collection', 'read108:collection'])]
    public ?string $lastname = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:collection', 'read108:collection'])]
    public ?string $name = null;

    #[ORM\Column(length: 128)]
    #[Groups(['read:collection', 'read108:collection'])]
    public ?string $email = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['read:collection','read108:collection'])]
    public ?string $phone = null;

    #[ORM\Column(length: 2)]
    #[Groups(['read:collection'])]
    public ?string $country = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:collection'])]
    public ?string $address = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $ip_address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $request_category = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $request = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $origin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:collection'])]
    public ?\DateTimeInterface $date_birth = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $company = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:collection'])]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: Sales::class)]
    #[Groups(['read:collection'])]
    public Collection $sales;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: Notes::class)]
    #[Groups(['read:collection'])]
    public Collection $notes;

    // #[ORM\OneToMany(mappedBy: 'contact', targetEntity: ContactCustomFields::class)]
    // #[Groups(['read:collection'])]
    public Collection $contactCustomFields;

    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: ContactBalances::class)]
    #[Groups(['read:collection'])]
    #[ApiSubresource]
    public Collection $contactBalances;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
        $this->notes = new ArrayCollection();
        //$this->contactCustomFields = new ArrayCollection();
        $this->contactBalances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(?string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function isGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(?string $ip_address): self
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function isRequestCategory(): ?string
    {
        return $this->request_category;
    }

    public function setRequestCategory(?string $request_category): self
    {
        $this->request_category = $request_category;

        return $this;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(?string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function isOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDateBirth(): ?\DateTimeInterface
    {
        return $this->date_birth;
    }

    public function setDateBirth(?\DateTimeInterface $date_birth): self
    {
        $this->date_birth = $date_birth;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

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
     * @return Collection<int, Sales>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sales $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setContact($this);
        }

        return $this;
    }

    public function removeSale(Sales $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getContact() === $this) {
                $sale->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notes>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setContact($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getContact() === $this) {
                $note->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactCustomFields>
     */
    public function getContactCustomFields(): Collection
    {
        return $this->contactCustomFields;
    }

    // public function addContactCustomField(ContactCustomFields $contactCustomField): self
    // {
    //     if (!$this->contactCustomFields->contains($contactCustomField)) {
    //         $this->contactCustomFields->add($contactCustomField);
    //         $contactCustomField->setContact($this);
    //     }

    //     return $this;
    // }

    public function removeContactCustomField(ContactCustomFields $contactCustomField): self
    {
        if ($this->contactCustomFields->removeElement($contactCustomField)) {
            // set the owning side to null (unless already changed)
            if ($contactCustomField->getContact() === $this) {
                $contactCustomField->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactBalances>
     */
    public function getContactBalances(): Collection
    {
        return $this->contactBalances;
    }

    public function addContactBalance(ContactBalances $contactBalance): self
    {
        if (!$this->contactBalances->contains($contactBalance)) {
            $this->contactBalances->add($contactBalance);
            $contactBalance->setContact($this);
        }

        return $this;
    }

    public function removeContactBalance(ContactBalances $contactBalance): self
    {
        if ($this->contactBalances->removeElement($contactBalance)) {
            // set the owning side to null (unless already changed)
            if ($contactBalance->getContact() === $this) {
                $contactBalance->setContact(null);
            }
        }

        return $this;
    }
}
