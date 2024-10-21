<?php

namespace App\Controller;

use App\Entity\Role;

class RoleController extends BasicController
{
    public function index(): void
    {
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';

        $roleRepository = $entityManager->getRepository(Role::class);
        $roles = $roleRepository->findAll();
        $this->twig->display('role/index.html.twig',
            [
                'roles' => $roles,
            ]);
    }

    public function add(): void
    {
        $this->twig->display('role/add.html.twig');

    }

    public function process($params = []) : void
    {
        $roleId = $params['id'] ?? null;
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        if(isset($_POST["name"]))
        {
            if($roleId !== null)
            {
                $roleRepository = $entityManager->getRepository
                (Role::class);
                $role = $roleRepository->findById($roleId);
                $role->setName($_POST["name"]);
                $entityManager->flush();
            }
            else
            {
                $role = new Role();
                $role->setName($_POST["name"]);
                $entityManager->persist($role);
                $entityManager->flush();
            }
            $url .= "role";
        }
        else
        {
            $url .= "role/add";
        }
        header($url);
        exit();
    }

    public function modify(array $params) : void
    {
        $roleId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $roleRepository = $entityManager->getRepository(Role::class);
        $role = $roleRepository->find($roleId);

        $this->twig->display('role/modify.html.twig',
            [
                'role' => $role,
            ]);
    }

    public function delete(array $params) : void
    {
        $roleId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $roleRepository = $entityManager->getRepository(Role::class);
        $role = $roleRepository->findById($roleId);
        $entityManager->remove($role);
        $entityManager->flush();
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        $url .= "role";
        header($url);
        exit();
    }
}