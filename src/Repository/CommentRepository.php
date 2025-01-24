<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findByPost($postId)
    {
        return $this->createQueryBuilder('comment')
            ->where('comment.post.id = :post')
//            ->join('comment.post', 'post')
            ->setParameter('post', $postId)
            ->getQuery()
            ->getResult();
    }
}