<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetpredefinedtextbyuserController;
use App\Controller\getPredefinedTextController;
use App\Repository\PredefindTextsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AddtextController;

#[ORM\Entity(repositoryClass: PredefindTextsRepository::class)]
#[ApiResource(
    collectionOperations: [
       'get' => [
            'method' => 'GET',
            'deserialize' => false,
            'controller' => getPredefinedTextController::class,
            'normalization_context' => [
                'groups' => ['read:collection3']
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
        'add_text' => [
            'method' => 'POST',
            'path' => '/add_text',
            'deserialize' => false,
            'controller' => AddtextController::class,
            'normalization_context' => [
                'groups' => ['read:collection3']
            ],
        ],
        
    ],
    itemOperations: [
       'get' => [
            'normalization_context' => [
                'groups' => ['read:collection3']
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
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'name' => 'partial'])]
class PredefindTexts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection3','read:collection14'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'predefindTexts')]
    #[Groups(['read:collection3'])]
    public ?Accounts $account = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read:collection3','read:collection14'])]
    public ?string $name = null;

    #[ORM\Column(length: 2)]
    #[Groups(['read:collection3','read:collection14'])]
    public ?string $language = null;

    #[ORM\Column]
    #[Groups(['read:collection3','read:collection14'])]
    public ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:collection3','read:collection14'])]
    public ?string $text = null;

    #[ORM\Column]
    #[Groups(['read:collection3','read:collection14'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:collection3'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:collection3'])]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'text', targetEntity: PredefinedTextUsers::class)]
    #[Groups(['read:collection3'])]
    public Collection $predefinedTextUsers;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:collection3','read:collection14'])]
    public ?string $shortCut = null;

    public function __construct()
    {
        $this->predefinedTextUsers = new ArrayCollection();
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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function isCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

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
     * @return Collection<int, PredefinedTextUsers>
     */
    public function getPredefinedTextUsers(): Collection
    {
        return $this->predefinedTextUsers;
    }

    public function addPredefinedTextUser(PredefinedTextUsers $predefinedTextUser): self
    {
        if (!$this->predefinedTextUsers->contains($predefinedTextUser)) {
            $this->predefinedTextUsers->add($predefinedTextUser);
            $predefinedTextUser->setText($this);
        }

        return $this;
    }

    public function removePredefinedTextUser(PredefinedTextUsers $predefinedTextUser): self
    {
        if ($this->predefinedTextUsers->removeElement($predefinedTextUser)) {
            // set the owning side to null (unless already changed)
            if ($predefinedTextUser->getText() === $this) {
                $predefinedTextUser->setText(null);
            }
        }

        return $this;
    }

    public function getShortCut(): ?string
    {
        return $this->shortCut;
    }

    public function setShortCut(?string $shortCut): self
    {
        $this->shortCut = $shortCut;

        return $this;
    }
}
