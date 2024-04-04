<?php

namespace App\Entity;



use App\Repository\TwoFactorAuthAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwoFactorAuthAccountRepository::class)]
#[ORM\Table(name: '`2fa_accounts`')]
class TwoFactorAuthAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: [
        "unsigned" => true
    ])]
    public ?int $id;


    #[ORM\Column(type: "integer")]
    public  ?int $customer_account_id;

    #[ORM\Column(type: "integer", nullable: true)]
    public ?int  $contact_id;



    #[ORM\Column(type: "smallint")]
    public $method;

    #[ORM\Column(type: "string", length: 64)]
    public  $receiver;

    #[ORM\Column(type: "smallint", nullable: true)]
    public ?int  $backup_method;

    #[ORM\Column(type: "string", length: 64, nullable: true)]
    public ?string  $backup_receiver;

    #[ORM\Column(type: "smallint")]
    public  ?int  $status;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_start;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end;

    

    public function __construct()
    {
    }
}
