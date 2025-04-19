<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Table(name: 'user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $lastName;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $firstName;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $emailAddress;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $username;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $password;

    #[ManyToOne(targetEntity: Role::class, inversedBy: "users")]
    #[JoinColumn(nullable: false, name: "role_id", referencedColumnName: "id")]
    private Role $role;

    #[ManyToMany(targetEntity: SocialNetwork::class, mappedBy: "users")]
    private Collection $socialNetworks;

    #[OneToMany(targetEntity: Post::class, mappedBy: "users")]
    private Collection $posts;

    #[OneToMany(targetEntity: Comment::class, mappedBy: "users")]
    private Collection $comments;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $isActive;

    public function __construct()
    {
        $this->socialNetworks = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param mixed $emailAddress
     */
    public function setEmailAddress($emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $options = ['cost' => 15];
        $hashedPassword =password_hash($password, PASSWORD_DEFAULT, $options);
        $this->password = $hashedPassword;
    }

    /**
     * @return Collection
     */
    public function getSocialNetworks(): Collection
    {
        return $this->socialNetworks;
    }

    /**
     * @param SocialNetwork $socialNetwork
     * @return void
     */
    public function addSocialNetwork(SocialNetwork $socialNetwork): void
    {
        $this->socialNetworks->add($socialNetwork);
    }

    /**
     * @param SocialNetwork $socialNetwork
     * @return void
     */
    public function removeSocialNetwork(SocialNetwork $socialNetwork): void
    {
        $this->socialNetworks->removeElement($socialNetwork);
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     * @return void
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return Collection
     */
    private function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * @return Collection
     */
    private function getComments(): Collection
    {
        return $this->comments;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
        $this->emailAddress = null;
        $this->username = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->emailAddress;
    }
}