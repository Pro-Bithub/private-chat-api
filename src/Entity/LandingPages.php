<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\LandingPagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AddPageController;
use App\Controller\LandingController;

#[ORM\Entity(repositoryClass: LandingPagesRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read16:collection','write13:collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'loadLandingPages' => [
            'method' => 'GET',
            'path' => '/loadLandingPages',
            'deserialize' => false,
            'controller' => LandingController::class,
            'normalization_context' => [
                'groups' => ['read16:collection','write13:collection']
            ],
        ],
        'post' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'add_page' => [
            'method' => 'POST',
            'path' => '/add_page',
            'deserialize' => false,
            'controller' => AddPageController::class,
            'normalization_context' => [
                'groups' => ['read16:collection','write13:collection']
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read16:collection','write13:collection']
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
        'delete' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
    ]    
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'name' => 'partial'])]
class LandingPages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read16:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'landingPages')]
    #[Groups(['read16:collection'])]
    public ?Accounts $account = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read16:collection'])]
    public ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read16:collection'])]
    public ?string $comment = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['read16:collection'])]
    public ?string $url = null;

    #[ORM\Column]
    #[Groups(['read16:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read16:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read16:collection'])]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'page', targetEntity: LandingPageFields::class)]
    #[Groups(['write13:collection'])]
    public Collection $landingPageFields;

    public function __construct()
    {
        $this->landingPageFields = new ArrayCollection();
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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
            $landingPageField->setPage($this);
        }

        return $this;
    }

    public function removeLandingPageField(LandingPageFields $landingPageField): self
    {
        if ($this->landingPageFields->removeElement($landingPageField)) {
            // set the owning side to null (unless already changed)
            if ($landingPageField->getPage() === $this) {
                $landingPageField->setPage(null);
            }
        }

        return $this;
    }
}
