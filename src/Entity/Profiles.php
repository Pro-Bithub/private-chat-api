<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Profiles as EntityProfiles;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ProfilesRepository::class)]
#[UniqueEntity(fields: ['login'], message: 'There is already an account with this email')]
#[ApiResource(
    collectionOperations: [
        'get',
        'post'
    ],
    itemOperations: [
        'get',
        'put',
        'delete'
    ]    
)]
class Profiles implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 84)]
    public ?string $accountId = null;

    #[ORM\Column(length: 84)]
    public ?string $username = null;

    #[ORM\Column(length: 128, unique: true)]
    public ?string $login = null;

    #[ORM\Column(length: 255)]
    public ?string $password = null;

    #[ORM\Column(nullable: true)]
    public ?string $u_type = null;

    #[ORM\Column(nullable: true)]
    public ?int $u_id = null;

    #[ORM\Column(length: 14, nullable: true)]
    public ?string $ip_address = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $browser_data = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $user_key = null;

        /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }
 /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

     /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSalt()
    {
        // You can leave this method empty unless you are using legacy password encoding
        // The "salt" is not commonly used in modern Symfony applications
    }

    public static function createFromPayload($username, array $payload): Profiles
    {
        //dd($payload);
        return (new Profiles())->setId($username)->setLogin($payload['username'] ?? '');
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(?string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isUType(): ?string
    {
        return $this->u_type;
    }

    public function setUType(?string $u_type): self
    {
        $this->u_type = $u_type;

        return $this;
    }

    public function getUId(): ?int
    {
        return $this->u_id;
    }

    public function setUId(?int $u_id): self
    {
        $this->u_id = $u_id;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(?string $ip_address): self
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function getBrowserData(): ?string
    {
        return $this->browser_data;
    }

    public function setBrowserData(?string $browser_data): self
    {
        $this->browser_data = $browser_data;

        return $this;
    }

    public function getUserKey(): ?string
    {
        return $this->user_key;
    }

    public function setUserKey(?string $user_key): self
    {
        $this->user_key = $user_key;

        return $this;
    }
}
