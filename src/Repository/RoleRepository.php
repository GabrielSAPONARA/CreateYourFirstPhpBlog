<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use App\Entity\Role;
class RoleRepository extends EntityRepository
{
    /**
     * @return Role[]
     */
    public function findAllRoles() : array
    {
        return $this->createQueryBuilder('role')
            ->orderBy('role.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $id
     * @return Role|null
     */
    public function findById(string $id) : ?Role
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return null|Role
     */
    public function findByName(string $name) : ?Role
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}