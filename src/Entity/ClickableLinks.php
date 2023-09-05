<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\AddlinkController;
use App\Controller\GetLinksController;
use App\Repository\ClickableLinksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClickableLinksRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'deserialize' => false,
            'controller' => GetLinksController::class,
            'normalization_context' => [
                'groups' => ['read:collection1', 'write1:collection', 'write21:collection']
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
        'add_link' => [
            'method' => 'POST',
            'path' => '/add_link',
            'deserialize' => false,
            'controller' => AddlinkController::class,
            'normalization_context' => [
                'groups' => ['read:collection1', 'write1:collection', 'write21:collection']
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection1', 'write1:collection', 'write21:collection']
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
class ClickableLinks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection1'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'clickableLinks')]
    #[Groups(['read:collection1'])]
    public ?Accounts $account = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read:collection1', 'write21:collection'])]
    public ?string $name = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['read:collection1', 'write21:collection'])]
    public ?string $url = null;

    #[ORM\Column]
    #[Groups(['read:collection1' ,'write21:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:collection1'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'link', targetEntity: ClickableLinksUsers::class)]
    #[Groups(['read:collection1' ,'write23:collection'])]
    #[ApiSubresource()]
    public Collection $clickableLinksUsers;

    public function __construct()
    {
        $this->clickableLinksUsers = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

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
     * @return Collection<int, ClickableLinksUsers>
     */
    public function getClickableLinksUsers(): Collection
    {
        return $this->clickableLinksUsers;
    }

    public function addClickableLinksUser(ClickableLinksUsers $clickableLinksUser): self
    {
        if (!$this->clickableLinksUsers->contains($clickableLinksUser)) {
            $this->clickableLinksUsers->add($clickableLinksUser);
            $clickableLinksUser->setLink($this);
        }

        return $this;
    }

    public function removeClickableLinksUser(ClickableLinksUsers $clickableLinksUser): self
    {
        if ($this->clickableLinksUsers->removeElement($clickableLinksUser)) {
            // set the owning side to null (unless already changed)
            if ($clickableLinksUser->getLink() === $this) {
                $clickableLinksUser->setLink(null);
            }
        }

        return $this;
    }
}
