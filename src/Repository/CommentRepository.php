<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    /**
     * @param $postId
     * @return Comment[]
     */
    public function findByPost($postId): array
    {
        return $this
            ->createQueryBuilder('comment')
            ->where('comment.isValidated = true')
            ->andWhere('post.id = :post')
            ->join('comment.post', 'post')
            ->setParameter('post', $postId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $id
     * @return Comment|null
     */
    public function findById($id): ?Comment
    {
        return $this
            ->createQueryBuilder('comment')
            ->where('comment.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param $isValidated
     * @return Comment[]
     */
    public function findByIsValidated($isValidated = false): array
    {
        return $this
            ->createQueryBuilder('comment')
            ->where('comment.isValidated = :isValidated')
            ->setParameter('isValidated', $isValidated)
            ->getQuery()
            ->getResult()
        ;
    }
}