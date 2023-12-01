<?php

namespace App\Entity;



use App\Repository\TwoFactorAuthCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwoFactorAuthCodeRepository::class)]
#[ORM\Table(name: '`2fa_codes`')]
class TwoFactorAuthCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: [
        "unsigned" => true
    ])]
    public ?int $id;


    #[ORM\Column(type: "integer")]
    public  ?int $account_id;
    #[ORM\Column(type: "integer")]
    public  ?int $code;
    



    #[ORM\Column(type: "smallint")]
    public  ?int  $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public ?\DateTimeInterface $date_creation;



    public function __construct()
    {
    }
}
