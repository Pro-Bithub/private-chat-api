<?php

namespace App\Entity;



use App\Repository\TwoFactorAuthRequestsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwoFactorAuthRequestsRepository::class)]
#[ORM\Table(name: '`2fa_requests`')]
class TwoFactorAuthRequests
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
    public  ?int $code_id;
    



    #[ORM\Column(type: "smallint")]
    public  ?int  $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public ?\DateTimeInterface $date_sent;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_verification;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_reject;

    public function __construct()
    {
    }
}
