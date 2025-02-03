<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'comment')]
class Comment
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $publishedDate;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $content;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    private User $user;

    #[ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    private Post $post;

    #[ORM\Column(type: "boolean", nullable: true)]
    private bool $isValidated;

    #[ORM\Column(type: "boolean", nullable: false)]
    private bool $isPublished;

    /**
     * @return \DateTime
     */
    public function getPublishedDate(): \DateTime
    {
        return $this->publishedDate;
    }

    /**
     * @param \DateTime $publishedDate
     */
    public function setPublishedDate(\DateTime $publishedDate): void
    {
        $this->publishedDate = $publishedDate;
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
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }


}