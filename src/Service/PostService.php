<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
class PostService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllPosts(): array
    {
        return $this->entityManager->getRepository(Post::class)->findAll();
    }

    public function savePost(array $data, ?int $postId, User $currentUser): Post
    {
        if((empty($data["Title"])) || (empty($data["Content"])) || (empty
            ($data["Chapo"])))
        {
            throw new \Exception("The title, the content or the chapo are empty.");
        }

        $postRepository = $this->entityManager->getRepository(Post::class);

        if($postId !== null)
        {
            $post = $postRepository->findById($postId);
            if(!$post)
            {
                throw new \Exception("The post with id {$postId} does not exist.");
            }
        }
        else
        {
            $post = new Post();
            $post->setUser($currentUser);
        }
        $post->setTitle($data["Title"]);
        $post->setContent($data["Content"]);
        $post->setChapo($data["Chapo"]);
        $post->setDateOfLastUpdate(new \DateTime('now', new \DateTimeZone('UTC')));
        $post->setIsPublished(isset($data["IsPublished"]) &&
                              (bool)$data['IsPublished']);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }
}