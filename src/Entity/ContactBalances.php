<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetbalancesbycontactController;
use App\Repository\ContactBalancesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactBalancesRepository::class)]
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
        'post' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'contact_balances' => [
            'method' => 'GET',
            'path' => '/getbalancesbycontact/{id}',
            'deserialize' => false,
            'controller' => GetbalancesbycontactController::class,
            'normalization_context' => [
                'groups' => ['read39:collection']
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
class ContactBalances
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection', 'read39:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contactBalances')]
    #[Groups(['read:collection', 'read39:collection'])]
    public ?Contacts $contact = null;

    #[ORM\Column]
    #[Groups(['read:collection', 'read39:collection'])]
    public ?string $request = null;

    #[ORM\Column]
    #[Groups(['read:collection', 'read39:collection'])]
    public ?int $request_id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read:collection', 'read39:collection'])]
    public ?int $balance = null;

    #[ORM\Column]
    #[Groups(['read:collection', 'read39:collection'])]
    public ?string $balance_type = null;

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

    public function isRequest(): ?string
    {
        return $this->request;
    }

    public function setRequest(string $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getRequestId(): ?int
    {
        return $this->request_id;
    }

    public function setRequestId(int $request_id): self
    {
        $this->request_id = $request_id;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function isBalanceType(): ?string
    {
        return $this->balance_type;
    }

    public function setBalanceType(string $balance_type): self
    {
        $this->balance_type = $balance_type;

        return $this;
    }
}
