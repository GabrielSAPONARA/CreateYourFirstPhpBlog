<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'post')]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', length: 500, nullable: false)]
    private string $chapo;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $content;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $dateOfLastUpdate;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private ?User $user;

    #[OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private Collection $comments;

    public function __construct()
    {
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
     * @return string
     */
    public function getChapo(): string
    {
        return $this->chapo;
    }

    /**
     * @param string $chapo
     */
    public function setChapo(string $chapo): void
    {
        $this->chapo = $chapo;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getDateOfLastUpdate(): string
    {
        return $this->dateOfLastUpdate
            ->setTimezone(new \DateTimeZone('Europe/Paris'))
            ->format('d/m/Y H:i:s');
    }

    /**
     * @param DateTime $dateOfLastUpdate
     */
    public function setDateOfLastUpdate(DateTime $dateOfLastUpdate): void
    {
        $this->dateOfLastUpdate = $dateOfLastUpdate;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): void
    {
        $this->comments->add($comment);
    }

    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
    }
}