<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            
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
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    public ?int $sender_id = null;

    #[ORM\Column]
    public ?int $receiver_id = null;

    #[ORM\Column(type: Types::TEXT)]
    public ?string $message = null;

    #[ORM\Column]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $date_sent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_seen = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderId(): ?int
    {
        return $this->sender_id;
    }

    public function setSenderId(int $sender_id): self
    {
        $this->sender_id = $sender_id;

        return $this;
    }

    public function getReceiverId(): ?int
    {
        return $this->receiver_id;
    }

    public function setReceiverId(int $receiver_id): self
    {
        $this->receiver_id = $receiver_id;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getDateSent(): ?\DateTimeInterface
    {
        return $this->date_sent;
    }

    public function setDateSent(\DateTimeInterface $date_sent): self
    {
        $this->date_sent = $date_sent;

        return $this;
    }

    public function getDateSeen(): ?\DateTimeInterface
    {
        return $this->date_seen;
    }

    public function setDateSeen(?\DateTimeInterface $date_seen): self
    {
        $this->date_seen = $date_seen;

        return $this;
    }
}
