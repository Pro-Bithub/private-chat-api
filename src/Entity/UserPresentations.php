<?php

namespace App\Entity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AddUserController;
use App\Repository\UserPresentationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\OpenApi\Model;
use App\Controller\UpdateUserController;

#[ORM\Entity(repositoryClass: UserPresentationsRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
            'openapi_context' => [
                //'security' => [],
            ],
        ],
        'post'=> [
            'method' => 'POST',
            'deserialize' => false,
            'controller' => AddUserController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'gender' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'website' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'role' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                    'nickname' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'country' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'languages' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'expertise' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'diploma' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'status' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'brandName' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'contactPhone' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'contactPhoneComment' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'contactMail' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ],
                                    'atrological_sign' => [
                                        'type' => 'string',
                                        'format' => 'string',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            
           
        ]
    ]
    ,normalizationContext : [
        'groups' => ['read:collection']
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:collection']
            ],
            'openapi_context' => [
                //'security' => [],
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
        'UpdateImage' => [
            'method' => 'POST',
            'path' => '/userImage/{id}/update',
            'deserialize' => false,
            'controller' => UpdateUserController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
           
                    
               
           
        ],
        
    ]    
)]
/**
 * @Vich\Uploadable()
 */
class UserPresentations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userPresentations')]
    public ?User $user = null;

    
    #[ORM\Column]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $gender = null;

    #[ORM\Column(length: 64)]
    #[Groups(['collection5','read:collection', 'collection6','read108:collection'])]
    public ?string $nickname = null;

    #[ORM\Column]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $role = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $skills = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $presentation = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $picture = null;

    #[ORM\Column(length: 64, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $brand_name = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $website = null;

    #[ORM\Column(length: 2, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $country = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $languages = null;

    #[ORM\Column(length: 32, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $contact_phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $contact_phone_comment = null;

    #[ORM\Column(length: 128, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $contact_mail = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $atrological_sign = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $expertise = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection5','read:collection', 'collection6'])]
    public ?string $diploma = null;

    #[ORM\Column]
    public ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTimeInterface $date_start = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $date_end = null;
    
    /**
     * @var File|null
     * @Vich\UploadableField(mapping="post_image", fileNameProperty="picture")
     */
    #[Groups(['read:collection'])]
    private ?File $file = null;
    
    #[ORM\Column(nullable: true)]
    #[Groups(['read:collection'])]
    private ?\DateTimeImmutable $updatedAt = null;
    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return UserPresentations
     */
    public function setFile(?File $file): UserPresentations
    {
        $this->file = $file;
        return $this;
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

    public function isGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function isRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }


    public function getPicture(): ?string
    {
        return $this->picture;
    }

    
    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getBrandName(): ?string
    {
        return $this->brand_name;
    }

    public function setBrandName(?string $brand_name): self
    {
        $this->brand_name = $brand_name;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLanguages(): ?string
    {
        return $this->languages;
    }

    public function setLanguages(?string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contact_phone;
    }

    public function setContactPhone(?string $contact_phone): self
    {
        $this->contact_phone = $contact_phone;

        return $this;
    }

    public function getContactPhoneComment(): ?string
    {
        return $this->contact_phone_comment;
    }

    public function setContactPhoneComment(?string $contact_phone_comment): self
    {
        $this->contact_phone_comment = $contact_phone_comment;

        return $this;
    }

    public function getContactMail(): ?string
    {
        return $this->contact_mail;
    }

    public function setContactMail(?string $contact_mail): self
    {
        $this->contact_mail = $contact_mail;

        return $this;
    }

    public function isAtrologicalSign(): ?string
    {
        return $this->atrological_sign;
    }

    public function setAtrologicalSign(?string $atrological_sign): self
    {
        $this->atrological_sign = $atrological_sign;

        return $this;
    }

    public function getExpertise(): ?string
    {
        return $this->expertise;
    }

    public function setExpertise(?string $expertise): self
    {
        $this->expertise = $expertise;

        return $this;
    }

    public function getDiploma(): ?string
    {
        return $this->diploma;
    }

    public function setDiploma(?string $diploma): self
    {
        $this->diploma = $diploma;

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

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
