<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetlogsbyaccountController;
use App\Controller\GetlogsbyuserController;
use App\Controller\GetLogsController;
use App\Repository\ContactLogsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactLogsRepository::class)]
class ContactLogs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection', 'read29:collection'])]
    public ?int $id = null;

    #[ORM\Column]
    #[Groups(['read29:collection'])]
    public ?int $profile_id = null;

    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?int $agent_id = null;

    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?int $action = null;

    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $element = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $element_value = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['read:collection','read29:collection'])]
    public ?\DateTimeInterface $log_date = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $message_id = null;

    #[ORM\Column(length: 64)]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $browsing_data = null;

   
}
