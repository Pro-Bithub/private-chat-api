<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SalesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\GetsalesController;

#[ORM\Entity(repositoryClass: SalesRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'deserialize' => false,
            'controller' => GetsalesController::class,
            'normalization_context' => [
                'groups' => ['read108:collection']
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
                'groups' => ['read108:collection']
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
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact'])]
class Sales
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read108:collection','read:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sales')]
    #[Groups(['read108:collection'])]
    public ?Contacts $contact = null;

    #[ORM\ManyToOne(inversedBy: 'sales')]
    #[Groups(['read108:collection','read:collection'])]
    public ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'sales')]
    #[Groups(['read108:collection','read:collection'])]
    public ?Plans $plan = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read108:collection','read:collection'])]
    public ?string $payment_method = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read108:collection'])]
    public ?int $provider_id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read108:collection'])]
    public ?int $p_id = null;


    #[ORM\Column(nullable: true)]
    #[Groups(['read108:collection'])]
    public ?int $tariff_id = null;

    

    #[ORM\Column]
    #[Groups(['read108:collection','read:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['read108:collection','read:collection'])]
    public ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['read108:collection'])]
    public ?\DateTimeInterface $date_end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contacts
    {
        return $this->contact;
    }

    public function setContact(?Contacts $contact): self
    {
        $this->contact = $contact;

        return $this;
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

    public function getPlan(): ?Plans
    {
        return $this->plan;
    }

    public function setPlan(?Plans $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function isPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(?string $payment_method): self
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    public function getProviderId(): ?int
    {
        return $this->provider_id;
    }

    public function setProviderId(?int $provider_id): self
    {
        $this->provider_id = $provider_id;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

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
