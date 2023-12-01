<?php

namespace App\Entity;



use App\Repository\SupporticketsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupporticketsRepository::class)]
#[ORM\Table(name: '`support_tickets`')]
class Supportickets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: [
        "unsigned" => true
    ])]
    public ?int $id;

    
    #[ORM\Column(type: "integer")]
    public  ?int $customer_account_id;




    #[ORM\Column(type: "string", length: 64)]
    public  $first_name;

    #[ORM\Column(type: "string", length: 64)]
    public  $last_name	;

    #[ORM\Column(type: "string", length: 128)]
    public  $email;

    #[ORM\Column(type: "string", length: 255)]
    public  $details;

    #[ORM\Column(type: "string", length: 255)]
    public  $browser;

    
    #[ORM\Column(type: "string", length: 15)]
    public  $ip_address;

    

    #[ORM\Column(type: "string", length: 128)]
    public  $source;


    #[ORM\Column(type: "smallint")]
    public  ?int  $subject;


    #[ORM\Column(type: "smallint")]
    public  ?int  $profile_type;



    #[ORM\Column(type: "smallint")]
    public  ?int $status ;

 
    #[ORM\Column(type: "integer")]
    public  ?int $profile_id ;

    


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public ?\DateTimeInterface $created_at;


    

    public function __construct()
    {
    }
}
