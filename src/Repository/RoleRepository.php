<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
class RoleRepository extends EntityRepository
{
    public function findAllRoles()
    {
        return $this->createQueryBuilder('role')
            ->orderBy('role.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findById(string $id)
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}