<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PlanDiscountsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlanDiscountsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['write5:collection', 'write7:collection']
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
                'groups' => ['write5:collection', 'write7:collection']
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
class PlanDiscounts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write5:collection','read19:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'planDiscounts')]
    public ?Plans $plan = null;

    #[ORM\Column(length: 100)]
    #[Groups(['write5:collection'])]
    public ?string $name = null;

    #[ORM\Column]
    #[Groups(['write5:collection','read19:collection'])]
    public ?string $discount_type = null;

    #[ORM\Column]
    #[Groups(['write5:collection','read19:collection'])]
    public ?string $discount_value = null;

    #[ORM\Column]
    #[Groups(['write5:collection','read19:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['write5:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['write5:collection'])]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'discount', targetEntity: PlanDiscountUsers::class)]
    #[Groups(['write5:collection','read19:collection'])]
    public Collection $planDiscountUsers;

    public function __construct()
    {
        $this->planDiscountUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isDiscountType(): ?string
    {
        return $this->discount_type;
    }

    public function setDiscountType(string $discount_type): self
    {
        $this->discount_type = $discount_type;

        return $this;
    }

    public function isDiscountValue(): ?string
    {
        return $this->discount_value;
    }

    public function setDiscountValue(string $discount_value): self
    {
        $this->discount_value = $discount_value;

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
     * @return Collection<int, PlanDiscountUsers>
     */
    public function getPlanDiscountUsers(): Collection
    {
        return $this->planDiscountUsers;
    }

    public function addPlanDiscountUser(PlanDiscountUsers $planDiscountUser): self
    {
        if (!$this->planDiscountUsers->contains($planDiscountUser)) {
            $this->planDiscountUsers->add($planDiscountUser);
            $planDiscountUser->setDiscount($this);
        }

        return $this;
    }

    public function removePlanDiscountUser(PlanDiscountUsers $planDiscountUser): self
    {
        if ($this->planDiscountUsers->removeElement($planDiscountUser)) {
            // set the owning side to null (unless already changed)
            if ($planDiscountUser->getDiscount() === $this) {
                $planDiscountUser->setDiscount(null);
            }
        }

        return $this;
    }
}
