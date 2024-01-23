<?php

namespace App\Entity;


use App\Repository\PlanTariffsRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: PlanTariffsRepository::class)]
class Currencies
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?int $id = null;




    #[ORM\Column(length: 3)]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?string $code = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['read4:collection', 'read19:collection', 'read108:collection', 'read:collection'])]
    public ?string $title = null;





    #[ORM\Column]
    #[Groups(['read4:collection' , 'read19:collection', 'read108:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read4:collection'])]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read4:collection'])]
    public ?\DateTimeInterface $date_end = null;

 

}
