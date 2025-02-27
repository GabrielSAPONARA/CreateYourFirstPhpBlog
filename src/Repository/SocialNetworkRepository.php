<?php

namespace App\Repository;

use App\Entity\SocialNetwork;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;

class SocialNetworkRepository extends EntityRepository
{
    public function findAllSocialNetworks() : array
    {
        return $this->createQueryBuilder('socialNetwork')
            ->orderBy('socialNetwork.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findById(string $id) : ?SocialNetwork
    {
        return $this->createQueryBuilder('socialNetwork')
            ->andWhere('socialNetwork.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}