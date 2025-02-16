<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CommentService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveComment(array $data, ?string $commentId = null, ?User
    $currentUser = null): Comment
    {
        if((empty($data["Content"])))
        {
            throw new \Exception("Empty comment content");
        }

        $commentRepository = $this->entityManager->getRepository(Comment::class);

        if($commentId !== null)
        {
            $comment = $commentRepository->findById($commentId)[0];
            if(!$comment)
            {
                throw new \Exception("The comment with the id $commentId does not exist");
            }
        }
        else
        {
            $comment = new Comment();
            $comment->setUser($currentUser);
            $comment->setPublishedDate(new \DateTime('now', new \DateTimeZone('UTC')));
            $comment->setIsValidated(false);
        }
        $comment->setContent($data["Content"]);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    public function findByPostId(string $postId): array
    {
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        return $commentRepository->findByPost($postId);
    }
}