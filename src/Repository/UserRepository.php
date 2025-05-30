<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class UserRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAll() : array
    {
        return $this->createQueryBuilder('user')
            ->orderBy('user.lastName, user.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function findById(string $id)
    {
        return $this->createQueryBuilder('user')
            ->where('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}