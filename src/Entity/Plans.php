<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetplansbyuserController;
use App\Repository\PlansRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\AddPlanContollerController;
use App\Controller\getPricingPlansController;

#[ORM\Entity(repositoryClass: PlansRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'deserialize' => false,
            'controller' => getPricingPlansController::class,
            'normalization_context' => [
                'groups' => ['read4:collection', 'write4:collection' ,'write5:collection' ,'write15:collection']
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
        'plans_account' => [
            'method' => 'GET',
            'path' => '/getplansbyaccount/{id}',
            'deserialize' => false,
            'controller' => GetplansbyuserController::class,
            'normalization_context' => [
                'groups' => ['read19:collection', 'write4:collection']
            ],
        ],
        'add_plans' => [
            'method' => 'POST',
            'path' => '/add_plans',
            'deserialize' => false,
            'controller' => AddPlanContollerController::class,
            'normalization_context' => [
                'groups' => ['read19:collection', 'write4:collection']
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read4:collection', 'write4:collection','write5:collection']
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
    // ]    ,
    // normalizationContext: [
    //     'groups' => ['read:collection'],
    //     'openapi_definition_name' => 'Collection',
    // ],
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'name' => 'partial'])]
class Plans
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'plans')]
    #[Groups(['write15:collection'])]
    public ?Accounts $account = null;

    #[ORM\Column(length: 1100)]
    #[Groups(['read4:collection','read19:collection', 'read108:collection', 'read:collection'])]
    public ?string $name = null;

    #[ORM\Column(length: 3)]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?string $currency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Groups(['read4:collection', 'read19:collection' , 'read108:collection', 'read:collection'])]
    public ?string $tariff = null;

    #[ORM\Column]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection'])]
    public ?string $billing_type = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(['read4:collection' , 'read19:collection', 'read108:collection'])]
    public ?int $billing_volume = null;

    #[ORM\Column]
    #[Groups(['read4:collection' , 'read19:collection', 'read108:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read4:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read4:collection'])]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: PlanUsers::class)]
    #[Groups(['read4:collection' ,'read19:collection'])]
    public Collection $planUsers;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: PlanTariffs::class)]
    #[Groups(['read4:collection' ,'read19:collection'])]
    public Collection $planTariffs;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: PlanDiscounts::class)]
    #[Groups(['write5:collection', 'read19:collection' ])]
    public Collection $planDiscounts;

    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: Sales::class)]
    public Collection $sales;

    #[ORM\Column(length: 2)]
    #[Groups(['read4:collection', 'read19:collection'])]
    public ?string $language = null;

    public function __construct()
    {
        $this->planUsers = new ArrayCollection();
        $this->planDiscounts = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->planTariffs = new ArrayCollection();
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getTariff(): ?string
    {
        return $this->tariff;
    }

    public function setTariff(string $tariff): self
    {
        $this->tariff = $tariff;

        return $this;
    }

    public function isBillingType(): ?string
    {
        return $this->billing_type;
    }

    public function setBillingType(string $billing_type): self
    {
        $this->billing_type = $billing_type;

        return $this;
    }

    public function getBillingVolume(): ?int
    {
        return $this->billing_volume;
    }

    public function setBillingVolume(int $billing_volume): self
    {
        $this->billing_volume = $billing_volume;

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
     * @return Collection<int, PlanUsers>
     */
    public function getPlanUsers(): collection
    {
        return $this->planUsers;
    }

    public function addPlanUser(PlanUsers $planUser): self
    {
        if (!$this->planUsers->contains($planUser)) {
            $this->planUsers->add($planUser);
            $planUser->setPlan($this);
        }

        return $this;
    }

    public function removePlanUser(PlanUsers $planUser): self
    {
        if ($this->planUsers->removeElement($planUser)) {
            // set the owning side to null (unless already changed)
            if ($planUser->getPlan() === $this) {
                $planUser->setPlan(null);
            }
        }

        return $this;
    }


        /**
     * @return Collection<int, PlanTariffs>
    */
    public function getPlanTariffs(): collection
    {
        return $this->planTariffs;
    }

    public function addplanTariff(PlanTariffs $planTariff): self
    {
        if (!$this->planTariffs->contains($planTariff)) {
            $this->planTariffs->add($planTariff);
            $planTariff->setPlan($this);
        }

        return $this;
    }

    public function removeplanTariff(PlanTariffs $planTariff): self
    {
        if ($this->planTariffs->removeElement($planTariff)) {
            // set the owning side to null (unless already changed)
            if ($planTariff->getPlan() === $this) {
                $planTariff->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlanDiscounts>
    */
    public function getPlanDiscounts(): collection
    {
        return $this->planDiscounts;
    }

    public function addPlanDiscount(PlanDiscounts $planDiscount): self
    {
        if (!$this->planDiscounts->contains($planDiscount)) {
            $this->planDiscounts->add($planDiscount);
            $planDiscount->setPlan($this);
        }

        return $this;
    }

    public function removePlanDiscount(PlanDiscounts $planDiscount): self
    {
        if ($this->planDiscounts->removeElement($planDiscount)) {
            // set the owning side to null (unless already changed)
            if ($planDiscount->getPlan() === $this) {
                $planDiscount->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sales>
     */
    public function getSales(): collection
    {
        return $this->sales;
    }

    public function addSale(Sales $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setPlan($this);
        }

        return $this;
    }

    public function removeSale(Sales $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getPlan() === $this) {
                $sale->setPlan(null);
            }
        }

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
}
