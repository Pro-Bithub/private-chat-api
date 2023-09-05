<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LandingPageFieldsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LandingPageFieldsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['write13:collection']
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
                'groups' => ['write13:collection']
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
class LandingPageFields
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write13:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'landingPageFields')]
    #[Groups(['read:collection'])]
    public ?LandingPages $page = null;

    #[ORM\ManyToOne(inversedBy: 'landingPageFields')]
    #[Groups(['write13:collection'])]
    public ?CustomFields $field = null;

    #[ORM\Column]
    #[Groups(['write13:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?LandingPages
    {
        return $this->page;
    }

    public function setPage(?LandingPages $page): self
    {
        $this->page = $page;

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
}
