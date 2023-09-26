<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UpdateUserPermissionController;
use App\Repository\UserPermissionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserPermissionsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'post'=> [
            'method' => 'POST',
            'path' => '/user/permissions/{id}',
            'deserialize' => false,
            'controller' => UpdateUserPermissionController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
           
        ],
     
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
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
class UserPermissions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userPermissions')]
    public ?User $user = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $pre_defined_messages = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $planning_management = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $package_creation = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $package_visibility = null;

    #[ORM\Column(length: 32)]
    #[Groups(['read:collection'])]
    public ?string $business_tools = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $communications = null;

    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $visitors_rating = null;

    #[ORM\Column]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isPreDefinedMessages(): ?string
    {
        return $this->pre_defined_messages;
    }

    public function setPreDefinedMessages(string $pre_defined_messages): self
    {
        $this->pre_defined_messages = $pre_defined_messages;

        return $this;
    }

    public function isPlanningManagement(): ?string
    {
        return $this->planning_management;
    }

    public function setPlanningManagement(string $planning_management): self
    {
        $this->planning_management = $planning_management;

        return $this;
    }

    public function isPackageCreation(): ?string
    {
        return $this->package_creation;
    }

    public function setPackageCreation(string $package_creation): self
    {
        $this->package_creation = $package_creation;

        return $this;
    }

    public function isPackageVisibility(): ?string
    {
        return $this->package_visibility;
    }

    public function setPackageVisibility(string $package_visibility): self
    {
        $this->package_visibility = $package_visibility;

        return $this;
    }

    public function getBusinessTools(): ?string
    {
        return $this->business_tools;
    }

    public function setBusinessTools(string $business_tools): self
    {
        $this->business_tools = $business_tools;

        return $this;
    }

    public function getCommunications(): ?string
    {
        return $this->communications;
    }

    public function setCommunications(?string $communications): self
    {
        $this->communications = $communications;

        return $this;
    }

    public function isVisitorsRating(): ?string
    {
        return $this->visitors_rating;
    }

    public function setVisitorsRating(string $visitors_rating): self
    {
        $this->visitors_rating = $visitors_rating;

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
}
