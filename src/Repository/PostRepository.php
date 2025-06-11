<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class PostRepository extends EntityRepository
{
    /**
     * @return Post[]
     */
    public function findAll(): array
    {
        return $this
            ->createQueryBuilder('post')
            ->orderBy('post.dateOfLastUpdate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $id
     * @return Post|null
     */
    public function findById(string $id): ?Post
    {
        return $this
            ->createQueryBuilder('post')
            ->andWhere('post.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param Uuid $id
     * @return Post[]
     */
    public function findByUser(Uuid $id): array
    {
        return $this
            ->createQueryBuilder('post')
            ->andWhere('user.id = :id')
            ->join('post.user', 'user')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }
}