<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findByPost($postId)
    {
        return $this->createQueryBuilder('comment')
            ->where('comment.isValidated = true')
            ->andWhere('post.id = :post')
            ->join('comment.post', 'post')
            ->setParameter('post', $postId)
            ->getQuery()
            ->getResult();
    }

    public function findById($id)
    {
        return $this->createQueryBuilder('comment')
            ->where('comment.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByIsValidated($isValidated = false)
    {
        return $this->createQueryBuilder('comment')
            ->where('comment.isValidated = :isValidated')
            ->setParameter('isValidated', $isValidated)
            ->getQuery()
            ->getResult();
    }
}