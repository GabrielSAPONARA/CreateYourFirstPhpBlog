<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function findAll() : array
    {
        return $this->createQueryBuilder('post')
                    ->orderBy('post.dateOfLastUpdate', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    public function findById(string $id)
    {
        return $this->createQueryBuilder('post')
                    ->andWhere('post.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
    }
}