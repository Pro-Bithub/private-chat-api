<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetcontactformsbyaccountController;
use App\Repository\ContactFormsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AddFormController;
use App\Controller\GetContactFormsController;

#[ORM\Entity(repositoryClass: ContactFormsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'deserialize' => false,
            'controller' => GetContactFormsController::class,
            'normalization_context' => [
                'groups' => ['read10:collection','write12:collection' ]
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
        'forms_account' => [
            'method' => 'GET',
            'path' => '/getformsbyaccount/{id}/formstype/{formtype}/status/{status}',
            'deserialize' => false,
            'controller' => GetcontactformsbyaccountController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
                
            ],
            'normalization_context' => [
                'groups' => ['read10:collection','write12:collection']
            ],
        ],
        'add_form' => [
            'method' => 'POST',
            'path' => '/add_form',
            'deserialize' => false,
            'controller' => AddFormController::class,
            'normalization_context' => [
                'groups' => ['read10:collection','write12:collection']
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read10:collection','write12:collection']
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
    ), 
    ApiFilter(
        SearchFilter::class,
        properties: ['id' => 'exact', 'form_type' => 'partial'],
    ),]
class ContactForms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read10:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contactForms')]
    #[Groups(['read10:collection'])]
    public ?Accounts $account = null;

    #[ORM\Column]
    #[Groups(['read10:collection'])]
    public ?string $form_type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $introduction = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $text_capture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $text_capture_before = null;

    

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $sendable_agents = null;


    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read10:collection'])]
    public ?int $source = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read10:collection'])]
    public ?int $agent_status = null;

    #[ORM\Column(length: 255,nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $message_capture = null;

    
    #[ORM\Column(length: 25, nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $button = null;
   





    #[ORM\Column(nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $waiting_time = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read10:collection'])]
    public ?string $friendly_name = null;

    #[ORM\Column]
    #[Groups(['read10:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read10:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read10:collection'])]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'form', targetEntity: ContactFormFields::class)]
    #[Groups(['write12:collection'])]
    public Collection $contactFormFields;

    public function __construct()
    {
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

    public function isFormType(): ?string
    {
        return $this->form_type;
    }

    public function setFormType(string $form_type): self
    {
        $this->form_type = $form_type;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getTextCapture(): ?string
    {
        return $this->text_capture;
    }

    public function setTextCapture(?string $text_capture): self
    {
        $this->text_capture = $text_capture;

        return $this;
    }

    public function getSendableAgents(): ?string
    {
        return $this->sendable_agents;
    }

    public function setSendableAgents(?string $sendable_agents): self
    {
        $this->sendable_agents = $sendable_agents;

        return $this;
    }

    public function isWaitingTime(): ?string
    {
        return $this->waiting_time;
    }

    public function setWaitingTime(?string $waiting_time): self
    {
        $this->waiting_time = $waiting_time;

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

    public function getFriendlyName(): ?string
    {
        return $this->friendly_name;
    }

    public function setFriendlyName(?string $friendly_name): self
    {
        $this->friendly_name = $friendly_name;

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
            $contactFormField->setForm($this);
        }

        return $this;
    }

    public function removeContactFormField(ContactFormFields $contactFormField): self
    {
        if ($this->contactFormFields->removeElement($contactFormField)) {
            // set the owning side to null (unless already changed)
            if ($contactFormField->getForm() === $this) {
                $contactFormField->setForm(null);
            }
        }

        return $this;
    }
}
