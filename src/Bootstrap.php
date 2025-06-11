<?php

namespace App;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class Bootstrap
{
    public function getEntityManager(): EntityManager
    {
        // Create a simple "default" Doctrine ORM configuration for Attributes
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__ . '/../src/Entity'],
            isDevMode: true,
        );

        // Configuring the database connection
        $connection = DriverManager::getConnection([
            'dbname'   => 'blog',
            'user'     => 'root',
            'password' => '',
            'host'     => 'localhost',
            'driver'   => 'pdo_mysql',
        ], $config);

        // Obtaining the entity manager
        $entityManager = new EntityManager($connection, $config);

        \Doctrine\DBAL\Types\Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');

        return $entityManager;
    }
}
