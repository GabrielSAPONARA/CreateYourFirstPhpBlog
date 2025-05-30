<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class PostService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllPosts(): array
    {
        $posts = $this->entityManager->getRepository(Post::class)
                                     ->findAll();
        usort($posts, function ($postA, $postB) {
            return $postB->getDateOfLastUpdate() <=> $postA->getDateOfLastUpdate();
        });
        return $posts;
    }

    /**
     * @param array $data
     * @param string|null $postId
     * @param User|null $currentUser
     * @return Post
     * @throws \DateMalformedStringException
     */
    public function savePost(array $data, ?string $postId = null, ?User
    $currentUser = null): Post
    {
        if((empty($data["Title"])) || (empty($data["Content"])) || (empty
            ($data["Chapo"])))
        {
            throw new \Exception("The title, the content or the chapo are empty.");
        }

        $postRepository = $this->entityManager->getRepository(Post::class);

        $dateOfLastUpdate = new \DateTime('now', new \DateTimeZone('UTC'));
        if($postId !== null)
        {
            $post = $postRepository->findById($postId)[0];
            if(!$post)
            {
                throw new \Exception("The post with id " . htmlspecialchars
                    ($postId, ENT_QUOTES, 'UTF-8') . " does not exist.");
            }
            if(($data["Title"] !== $post->getTitle()) || ($data["Content"] !== $post->getContent()) || ($data["Chapo"] !== $post->getChapo()) )
            {
                $post->setDateOfLastUpdate($dateOfLastUpdate);
            }
        }
        else
        {
            $post = new Post();
            $post->setUser($currentUser);
            $post->setDateOfLastUpdate($dateOfLastUpdate);
        }
        $post->setTitle($data["Title"]);
        $post->setContent($data["Content"]);
        $post->setChapo($data["Chapo"]);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    /**
     * @param string $postId
     * @return Post|null
     * @throws \Exception
     */
    public function findByPostId(string $postId): ?Post
    {
        $postRepository = $this->entityManager->getRepository(Post::class);
        $post = $postRepository->findById($postId)[0];

        if(!$post)
        {
            throw new \Exception("The post with id ".  htmlspecialchars
                ($postId, ENT_QUOTES, 'UTF-8') . " does not exist.");
        }

        return $post;
    }

    /**
     * @return array|null
     */
    public function findPostToValidate() : ?array
    {
        $postRepository = $this->entityManager->getRepository(Post::class);
        return $postRepository->findByIsValidated(false);
    }
}