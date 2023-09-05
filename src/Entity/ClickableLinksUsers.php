<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetlinksbyuserController;
use App\Repository\ClickableLinksUsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClickableLinksUsersRepository::class)]
#[ApiResource(
    collectionOperations: [
       'get' => [
            'normalization_context' => [
                'groups' => ['write1:collection', 'write21:collection']
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
        'clickable_links_users' => [
            'method' => 'GET',
            'path' => '/getlinksbyuser/{id}',
            'deserialize' => false,
            'controller' => GetlinksbyuserController::class,
            'normalization_context' => [
                'groups' => ['write1:collection','write21:collection']
            ],
        ],
    ],
    itemOperations: [
       'get' => [
            'normalization_context' => [
                'groups' => ['write1:collection', 'write21:collection']
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
        
    ],
)]
class ClickableLinksUsers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write1:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'clickableLinksUsers')]
    #[Groups(['write21:collection'])]
    public ?ClickableLinks $link = null;

    #[ORM\ManyToOne(inversedBy: 'clickableLinksUsers')]
    #[Groups(['write1:collection'])]
    public ?User $user = null;

    #[ORM\Column]
    #[Groups(['write1:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
   
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?ClickableLinks
    {
        return $this->link;
    }

    public function setLink(?ClickableLinks $link): self
    {
        $this->link = $link;

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
}
