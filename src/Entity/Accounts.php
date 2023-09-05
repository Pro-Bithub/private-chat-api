<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\AccountsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AccountsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get'=> [
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
        'get'=> [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
            ],
            'normalization_context' => [
                'groups' => ['read:collection3']
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
class Accounts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['write15:collection', 'write4:collection', 'read:collection3'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['read:collection3'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read:collection3'])]

    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:collection3'])]

    private ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read:collection3'])]

    private ?\DateTimeInterface $date_end = null;

    #[ORM\Column]
    #[Groups(['read:collection3'])]

    public ?string $api_key = null;

    // #[ORM\OneToMany(mappedBy: 'account', targetEntity: User::class)]
    // #[ApiSubresource]
    // private Collection $users;

    // #[ORM\OneToMany(mappedBy: 'account', targetEntity: Registrations::class)]
    // private Collection $registrations;

    // #[ORM\OneToMany(mappedBy: 'account', targetEntity: Profiles::class)]
    // private Collection $profiles;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: PredefindTexts::class)]
    private Collection $predefindTexts;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Plans::class)]
    #[ApiSubresource]
    private Collection $plans;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: LandingPages::class)]
    private Collection $landingPages;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: CustomFields::class)]
    private Collection $customFields;

    // #[ORM\OneToMany(mappedBy: 'account', targetEntity: Contacts::class)]
    // private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: ContactForms::class)]
    #[ApiSubresource]
    private Collection $contactForms;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: ClickableLinks::class)]
    private Collection $clickableLinks;

    public function __construct()
    {
        //$this->users = new ArrayCollection();
        // $this->registrations = new ArrayCollection();
        // $this->profiles = new ArrayCollection();
         $this->predefindTexts = new ArrayCollection();
         $this->plans = new ArrayCollection();
         $this->landingPages = new ArrayCollection();
         $this->customFields = new ArrayCollection();
        // $this->contacts = new ArrayCollection();
         $this->contactForms = new ArrayCollection();
         $this->clickableLinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiKey(string $api_key): self
    {
        $this->api_key = $api_key;

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

    // /**
    //  * @return Collection<int, User>
    //  */
    // public function getUsers(): Collection
    // {
    //     return $this->users;
    // }

    // // public function addUser(User $user): self
    // // {
    // //     if (!$this->users->contains($user)) {
    // //         $this->users->add($user);
    // //         $user->setAccount($this);
    // //     }

    // //     return $this;
    // // }

    // // public function removeUser(User $user): self
    // // {
    // //     if ($this->users->removeElement($user)) {
    // //         // set the owning side to null (unless already changed)
    // //         if ($user->getAccount() === $this) {
    // //             $user->setAccount(null);
    // //         }
    // //     }

    // //     return $this;
    // // }

    // /**
    //  * @return Collection<int, Registrations>
    //  */
    // public function getRegistrations(): Collection
    // {
    //     return $this->registrations;
    // }

    // // public function addRegistration(Registrations $registration): self
    // // {
    // //     if (!$this->registrations->contains($registration)) {
    // //         $this->registrations->add($registration);
    // //         $registration->setAccount($this);
    // //     }

    // //     return $this;
    // // }

    // public function removeRegistration(Registrations $registration): self
    // {
    //     if ($this->registrations->removeElement($registration)) {
    //         // set the owning side to null (unless already changed)
    //         if ($registration->getAccount() === $this) {
    //             $registration->setAccount(null);
    //         }
    //     }

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Profiles>
    //  */
    // public function getProfiles(): Collection
    // {
    //     return $this->profiles;
    // }

    // public function addProfile(Profiles $profile): self
    // {
    //     if (!$this->profiles->contains($profile)) {
    //         $this->profiles->add($profile);
    //         $profile->setAccount($this);
    //     }

    //     return $this;
    // }

    // public function removeProfile(Profiles $profile): self
    // {
    //     if ($this->profiles->removeElement($profile)) {
    //         // set the owning side to null (unless already changed)
    //         if ($profile->getAccount() === $this) {
    //             $profile->setAccount(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, PredefindTexts>
     */
    public function getPredefindTexts(): Collection
    {
        return $this->predefindTexts;
    }

    public function addPredefindText(PredefindTexts $predefindText): self
    {
        if (!$this->predefindTexts->contains($predefindText)) {
            $this->predefindTexts->add($predefindText);
            $predefindText->setAccount($this);
        }

        return $this;
    }

    public function removePredefindText(PredefindTexts $predefindText): self
    {
        if ($this->predefindTexts->removeElement($predefindText)) {
            // set the owning side to null (unless already changed)
            if ($predefindText->getAccount() === $this) {
                $predefindText->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Plans>
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plans $plan): self
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->setAccount($this);
        }

        return $this;
    }

    public function removePlan(Plans $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getAccount() === $this) {
                $plan->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LandingPages>
     */
    public function getLandingPages(): Collection
    {
        return $this->landingPages;
    }

    public function addLandingPage(LandingPages $landingPage): self
    {
        if (!$this->landingPages->contains($landingPage)) {
            $this->landingPages->add($landingPage);
            $landingPage->setAccount($this);
        }

        return $this;
    }

    public function removeLandingPage(LandingPages $landingPage): self
    {
        if ($this->landingPages->removeElement($landingPage)) {
            // set the owning side to null (unless already changed)
            if ($landingPage->getAccount() === $this) {
                $landingPage->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CustomFields>
     */
    public function getCustomFields(): Collection
    {
        return $this->customFields;
    }

    public function addCustomField(CustomFields $customField): self
    {
        if (!$this->customFields->contains($customField)) {
            $this->customFields->add($customField);
            $customField->setAccount($this);
        }

        return $this;
    }

    public function removeCustomField(CustomFields $customField): self
    {
        if ($this->customFields->removeElement($customField)) {
            // set the owning side to null (unless already changed)
            if ($customField->getAccount() === $this) {
                $customField->setAccount(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Contacts>
    //  */
    // public function getContacts(): Collection
    // {
    //     return $this->contacts;
    // }

    // public function addContact(Contacts $contact): self
    // {
    //     if (!$this->contacts->contains($contact)) {
    //         $this->contacts->add($contact);
    //         $contact->setAccount($this);
    //     }

    //     return $this;
    // }

    // public function removeContact(Contacts $contact): self
    // {
    //     if ($this->contacts->removeElement($contact)) {
    //         // set the owning side to null (unless already changed)
    //         if ($contact->getAccount() === $this) {
    //             $contact->setAccount(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, ContactForms>
     */
    public function getContactForms(): Collection
    {
        return $this->contactForms;
    }

    public function addContactForm(ContactForms $contactForm): self
    {
        if (!$this->contactForms->contains($contactForm)) {
            $this->contactForms->add($contactForm);
            $contactForm->setAccount($this);
        }

        return $this;
    }

    public function removeContactForm(ContactForms $contactForm): self
    {
        if ($this->contactForms->removeElement($contactForm)) {
            // set the owning side to null (unless already changed)
            if ($contactForm->getAccount() === $this) {
                $contactForm->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClickableLinks>
     */
    public function getClickableLinks(): Collection
    {
        return $this->clickableLinks;
    }

    public function addClickableLink(ClickableLinks $clickableLink): self
    {
        if (!$this->clickableLinks->contains($clickableLink)) {
            $this->clickableLinks->add($clickableLink);
            $clickableLink->setAccount($this);
        }

        return $this;
    }

    public function removeClickableLink(ClickableLinks $clickableLink): self
    {
        if ($this->clickableLinks->removeElement($clickableLink)) {
            // set the owning side to null (unless already changed)
            if ($clickableLink->getAccount() === $this) {
                $clickableLink->setAccount(null);
            }
        }

        return $this;
    }
}
