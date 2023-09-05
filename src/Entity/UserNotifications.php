<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetusernotificationsbyuserController;
use App\Repository\UserNotificationsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserNotificationsRepository::class)]
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
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'user_id' => [
            'method' => 'GET',
            'path' => '/getusernotificationsbyuserid/{id}',
            'deserialize' => false,
            'controller' => GetusernotificationsbyuserController::class,
            'normalization_context' => [
                'groups' => ['collection50']
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
        'put' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'delete' => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
    ]    
)]
class UserNotifications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userNotifications')]
    #[Groups(['collection50'])]
    public ?User $user = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $visitor_register = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $visitor_login = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $plan_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $contact_form_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $predefined_text_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $links_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $user_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $landing_page_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $contact_actions = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $email_notifications = null;

    #[ORM\Column]
    #[Groups(['collection5','collection50','read:collection', 'collection6'])]
    public ?string $sales = null;

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

    public function isVisitorRegister(): ?string
    {
        return $this->visitor_register;
    }

    public function setVisitorRegister(string $visitor_register): self
    {
        $this->visitor_register = $visitor_register;

        return $this;
    }

    public function isVisitorLogin(): ?string
    {
        return $this->visitor_login;
    }

    public function setVisitorLogin(string $visitor_login): self
    {
        $this->visitor_login = $visitor_login;

        return $this;
    }

    public function isPlanActions(): ?string
    {
        return $this->plan_actions;
    }

    public function setPlanActions(string $plan_actions): self
    {
        $this->plan_actions = $plan_actions;

        return $this;
    }

    public function isContactFormActions(): ?string
    {
        return $this->contact_form_actions;
    }

    public function setContactFormActions(string $contact_form_actions): self
    {
        $this->contact_form_actions = $contact_form_actions;

        return $this;
    }

    public function isPredefinedTextActions(): ?string
    {
        return $this->predefined_text_actions;
    }

    public function setPredefinedTextActions(string $predefined_text_actions): self
    {
        $this->predefined_text_actions = $predefined_text_actions;

        return $this;
    }

    public function isLinksActions(): ?string
    {
        return $this->links_actions;
    }

    public function setLinksActions(string $links_actions): self
    {
        $this->links_actions = $links_actions;

        return $this;
    }

    public function isUserActions(): ?string
    {
        return $this->user_actions;
    }

    public function setUserActions(string $user_actions): self
    {
        $this->user_actions = $user_actions;

        return $this;
    }

    public function isLandingPageActions(): ?string
    {
        return $this->landing_page_actions;
    }

    public function setLandingPageActions(string $landing_page_actions): self
    {
        $this->landing_page_actions = $landing_page_actions;

        return $this;
    }

    public function isEmailNotifications(): ?string
    {
        return $this->email_notifications;
    }

    public function setEmailNotifications(string $email_notifications): self
    {
        $this->email_notifications = $email_notifications;

        return $this;
    }

    public function isContactActions(): ?string
    {
        return $this->contact_actions;
    }

    public function setContactActions(string $contact_actions): self
    {
        $this->contact_actions = $contact_actions;

        return $this;
    }

    public function isSales(): ?string
    {
        return $this->sales;
    }

    public function setSales(string $sales): self
    {
        $this->sales = $sales;

        return $this;
    }
}
