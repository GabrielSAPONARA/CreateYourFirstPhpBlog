<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class PostRepository extends EntityRepository
{
    public function findAll() : array
    {
        return $this->createQueryBuilder('post')
                    ->orderBy('post.dateOfLastUpdate', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    public function findByIsPublished($isPublished = true) : array
    {
        return $this->createQueryBuilder('post')
                    ->where('post.isPublished = true')
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

    public function findByUser(Uuid $id)
    {
        return $this->createQueryBuilder('post')
                    ->andWhere('user.id = :id')
                    ->join('post.user', 'user')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getResult();
    }

    public function findByIsValidated($isValidated = false)
    {
        return $this->createQueryBuilder('post')
            ->where('post.isValidated = false')
            ->orderBy('post.dateOfLastUpdate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}