<?php

namespace App\Entity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\CreateUserController;
use App\Controller\GetuserByIdController;
use App\Controller\GetusersbyaccountController;
use App\Controller\UpdateUserPasswordController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\GetUsersController;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection','collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'getuser' => [
            'method' => 'GET',
            'path' => '/getuserdata/{id}',
            'deserialize' => false,
            'controller' => GetUsersController::class,
            'normalization_context' => [
                'groups' => ['read:collection','collection']
            ],
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
        ],
        'post'=> [
            'method' => 'POST',
            'deserialize' => false,
            'controller' => CreateUserController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
           
        ],
        'user_account' => [
            'method' => 'GET',
            'path' => '/getuserbyaccount/{id}',
            'deserialize' => false,
            'controller' => GetusersbyaccountController::class,
            'normalization_context' => [
                'groups' => ['collection5']
            ],
        ],
        'user_id' => [
            'method' => 'GET',
            'path' => '/getuserbyid/{id}',
            'deserialize' => false,
            'controller' => GetuserByIdController::class,
            'normalization_context' => [
                'groups' => ['collection6']
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection', 'collection']
            ],
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
        'UpdatePassword' => [
            'method' => 'POST',
            'path' => '/user/{id}/update',
            'deserialize' => false,
            'controller' => UpdateUserPasswordController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'password' => [
                                        'type' => 'string',
                                        'format' => 'password',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
           
                    
               
           
        ],
    ],
    subresourceOperations: [
        'normalization_context' => [
            'groups' => ['write23:collection']
        ],
    ]
)]

#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection50','read108:collection','collection6','read:collection3','read29:collection','write3:collection','write14:collection','collection5','read:collection','read19:collection','write:collection', 'write1:collection','write3:collection', 'write5:collection', 'write7:collection', 'write23:collection', 'read9:collection'])]
    public ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['read:collection','collection5'])]
    public ?string $email = null;

    #[ORM\Column(type: Types::JSON ,nullable: true)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['read:collection'])]
    public ?string $password = null;

    // #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\Column]
    #[Groups(['read:collection','read29:collection'])]
    public ?string $accountId = null;

   

    #[ORM\Column(length: 84, nullable: true)]
    #[Groups(['read:collection', 'write:collection','read108:collection','read29:collection' ,'collection5'])]
    public ?string $firstname = null;

    #[ORM\Column(length: 84, nullable: true)]
    #[Groups(['read:collection' , 'write:collection', 'read108:collection','read29:collection' ,'collection5'])]
    public ?string $lastname = null;

    #[ORM\Column(length: 128, nullable: true)]
    public ?string $login = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $notification_mail = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $notification_audio = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $notification_browser = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['read:collection'])]
    private ?string $timezone = null;

    #[ORM\Column(nullable: true)]
    private ?string $shortcut = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserRights::class)]
    #[Groups(['read:collection'])]
    private Collection $userRights;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPresentations::class)]
    #[Groups(['collection5','read:collection', 'collection6','read108:collection'])]
    private Collection $userPresentations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPlanning::class)]
    private Collection $userPlannings;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPermissions::class)]
    #[Groups(['read:collection'])]
    private Collection $userPermissions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserNotifications::class)]
    #[Groups(['collection5','read:collection','collection6'])]
    private Collection $userNotifications;

    // #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: UserLogs::class)]
    // #[Groups(['read:collection'])]
    // private Collection $userLogs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PredefinedTextUsers::class)]
    #[ApiSubresource]
    private Collection $predefinedTextUsers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PlanUsers::class)]
    private Collection $planUsers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PlanDiscountUsers::class)]
    private Collection $planDiscountUsers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Sales::class)]
    private Collection $sales;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notes::class)]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ClickableLinksUsers::class)]
    #[ApiSubresource()]
    #[Groups(['read:collection'])]
    private Collection $clickableLinksUsers;

    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    public ?string $gender = null;

        




    public function __construct()
    {
        $this->userRights = new ArrayCollection();
        $this->userPresentations = new ArrayCollection();
        $this->userPlannings = new ArrayCollection();
        $this->userPermissions = new ArrayCollection();
        $this->userNotifications = new ArrayCollection();
        //$this->userLogs = new ArrayCollection();
        $this->predefinedTextUsers = new ArrayCollection();
        $this->planUsers = new ArrayCollection();
        $this->planDiscountUsers = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->clickableLinksUsers = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSalt()
    {
        // You can leave this method empty unless you are using legacy password encoding
        // The "salt" is not commonly used in modern Symfony applications
    }

    public function getUsername()
    {
        // Implement this method to return the unique identifier for the user
        return $this->email;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

   
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function isNotificationMail(): ?string
    {
        return $this->notification_mail;
    }

    public function setNotificationMail(string $notification_mail): self
    {
        $this->notification_mail = $notification_mail;

        return $this;
    }

    public function isNotificationAudio(): ?string
    {
        return $this->notification_audio;
    }

    public function setNotificationAudio(string $notification_audio): self
    {
        $this->notification_audio = $notification_audio;

        return $this;
    }

    public function isNotificationBrowser(): ?string
    {
        return $this->notification_browser;
    }

    public function setNotificationBrowser(string $notification_browser): self
    {
        $this->notification_browser = $notification_browser;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function isShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function setShortcut(?string $shortcut): self
    {
        $this->shortcut = $shortcut;

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

    /**
     * @return Collection<int, UserRights>
     */
    public function getUserRights()
    {
        return $this->userRights->getValues();
    }

    public function addUserRight(UserRights $userRight): self
    {
        if (!$this->userRights->contains($userRight)) {
            $this->userRights->add($userRight);
            $userRight->setUser($this);
        }

        return $this;
    }

    public function removeUserRight(UserRights $userRight): self
    {
        if ($this->userRights->removeElement($userRight)) {
            // set the owning side to null (unless already changed)
            if ($userRight->getUser() === $this) {
                $userRight->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserPresentations>
     */
    public function getUserPresentations()
    {
        return $this->userPresentations->getValues();
    }

    public function addUserPresentation(UserPresentations $userPresentation): self
    {
        if (!$this->userPresentations->contains($userPresentation)) {
            $this->userPresentations->add($userPresentation);
            $userPresentation->setUser($this);
        }

        return $this;
    }

    public function removeUserPresentation(UserPresentations $userPresentation): self
    {
        if ($this->userPresentations->removeElement($userPresentation)) {
            // set the owning side to null (unless already changed)
            if ($userPresentation->getUser() === $this) {
                $userPresentation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserPlanning>
     */
    public function getUserPlannings(): Collection
    {
        return $this->userPlannings;
    }

    public function addUserPlanning(UserPlanning $userPlanning): self
    {
        if (!$this->userPlannings->contains($userPlanning)) {
            $this->userPlannings->add($userPlanning);
            $userPlanning->setUser($this);
        }

        return $this;
    }

    public function removeUserPlanning(UserPlanning $userPlanning): self
    {
        if ($this->userPlannings->removeElement($userPlanning)) {
            // set the owning side to null (unless already changed)
            if ($userPlanning->getUser() === $this) {
                $userPlanning->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserPermissions>
     */
    public function getUserPermissions(): Collection
    {
        return $this->userPermissions;
    }

    public function addUserPermission(UserPermissions $userPermission): self
    {
        if (!$this->userPermissions->contains($userPermission)) {
            $this->userPermissions->add($userPermission);
            $userPermission->setUser($this);
        }

        return $this;
    }

    public function removeUserPermission(UserPermissions $userPermission): self
    {
        if ($this->userPermissions->removeElement($userPermission)) {
            // set the owning side to null (unless already changed)
            if ($userPermission->getUser() === $this) {
                $userPermission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserNotifications>
     */
    public function getUserNotifications(): Collection
    {
        return $this->userNotifications;
    }

    public function addUserNotification(UserNotifications $userNotification): self
    {
        if (!$this->userNotifications->contains($userNotification)) {
            $this->userNotifications->add($userNotification);
            $userNotification->setUser($this);
        }

        return $this;
    }

    public function removeUserNotification(UserNotifications $userNotification): self
    {
        if ($this->userNotifications->removeElement($userNotification)) {
            // set the owning side to null (unless already changed)
            if ($userNotification->getUser() === $this) {
                $userNotification->setUser(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, UserLogs>
    //  */
    // public function getUserLogs(): Collection
    // {
    //     return $this->userLogs;
    // }

    // public function addUserLog(UserLogs $userLog): self
    // {
    //     if (!$this->userLogs->contains($userLog)) {
    //         $this->userLogs->add($userLog);
    //         $userLog->setUser($this);
    //     }

    //     return $this;
    // }

    // public function removeUserLog(UserLogs $userLog): self
    // {
    //     if ($this->userLogs->removeElement($userLog)) {
    //         // set the owning side to null (unless already changed)
    //         if ($userLog->getUser() === $this) {
    //             $userLog->setUser(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, PredefinedTextUsers>
     */
    public function getPredefinedTextUsers(): Collection
    {
        return $this->predefinedTextUsers;
    }

    public function addPredefinedTextUser(PredefinedTextUsers $predefinedTextUser): self
    {
        if (!$this->predefinedTextUsers->contains($predefinedTextUser)) {
            $this->predefinedTextUsers->add($predefinedTextUser);
            $predefinedTextUser->setUser($this);
        }

        return $this;
    }

    public function removePredefinedTextUser(PredefinedTextUsers $predefinedTextUser): self
    {
        if ($this->predefinedTextUsers->removeElement($predefinedTextUser)) {
            // set the owning side to null (unless already changed)
            if ($predefinedTextUser->getUser() === $this) {
                $predefinedTextUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlanUsers>
     */
    public function getPlanUsers(): Collection
    {
        return $this->planUsers;
    }

    public function addPlanUser(PlanUsers $planUser): self
    {
        if (!$this->planUsers->contains($planUser)) {
            $this->planUsers->add($planUser);
            $planUser->setUser($this);
        }

        return $this;
    }

    public function removePlanUser(PlanUsers $planUser): self
    {
        if ($this->planUsers->removeElement($planUser)) {
            // set the owning side to null (unless already changed)
            if ($planUser->getUser() === $this) {
                $planUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlanDiscountUsers>
     */
    public function getPlanDiscountUsers(): Collection
    {
        return $this->planDiscountUsers;
    }

    public function addPlanDiscountUser(PlanDiscountUsers $planDiscountUser): self
    {
        if (!$this->planDiscountUsers->contains($planDiscountUser)) {
            $this->planDiscountUsers->add($planDiscountUser);
            $planDiscountUser->setUser($this);
        }

        return $this;
    }

    public function removePlanDiscountUser(PlanDiscountUsers $planDiscountUser): self
    {
        if ($this->planDiscountUsers->removeElement($planDiscountUser)) {
            // set the owning side to null (unless already changed)
            if ($planDiscountUser->getUser() === $this) {
                $planDiscountUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sales>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sales $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setUser($this);
        }

        return $this;
    }

    public function removeSale(Sales $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getUser() === $this) {
                $sale->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notes>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUser() === $this) {
                $note->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClickableLinksUsers>
     */
    public function getClickableLinksUsers(): Collection
    {
        return $this->clickableLinksUsers;
    }

    public function addClickableLinksUser(ClickableLinksUsers $clickableLinksUser): self
    {
        if (!$this->clickableLinksUsers->contains($clickableLinksUser)) {
            $this->clickableLinksUsers->add($clickableLinksUser);
            $clickableLinksUser->setUser($this);
        }

        return $this;
    }

    public function removeClickableLinksUser(ClickableLinksUsers $clickableLinksUser): self
    {
        if ($this->clickableLinksUsers->removeElement($clickableLinksUser)) {
            // set the owning side to null (unless already changed)
            if ($clickableLinksUser->getUser() === $this) {
                $clickableLinksUser->setUser(null);
            }
        }

        return $this;
    }

    public function isGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public static function createFromPayload($username, array $payload): User
    {
        //dd($payload);
        return (new User())->setId($username)->setEmail($payload['username'] ?? '');
    }
}
