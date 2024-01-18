<?php

namespace App\Entity;


use App\Repository\PlanTariffsRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: PlanTariffsRepository::class)]
class PlanTariffs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'plan_tariffs')]
    #[Groups(['write15:collection'])]
    public ?Plans $plan = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Groups(['read4:collection', 'read19:collection' , 'read108:collection', 'read:collection'])]
    public ?string $price = null;

    #[ORM\Column(length: 3)]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?string $currency = null;

    #[ORM\Column(length: 2)]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?string $country = null;


    #[ORM\Column(length: 2)]
    #[Groups(['read4:collection', 'read19:collection'])]
    public ?string $language = null;



    #[ORM\Column]
    #[Groups(['read4:collection' , 'read19:collection', 'read108:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read4:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read4:collection'])]
    public ?\DateTimeInterface $date_end = null;

    public function getPlan(): ?Plans
    {
        return $this->plan;
    }

    public function setPlan(?Plans $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

}
