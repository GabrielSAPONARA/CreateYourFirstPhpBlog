<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;

class UserController extends BasicController
{
    public function index(): void
    {
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';

        $userRepository = $entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        $this->twig->display('user/index.html.twig',
            [
                'users' => $users,
            ]);
    }

    public function add(): void
    {
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $roleRepository = $entityManager->getRepository(Role::class);
        $roles = $roleRepository->findAll();
        $this->twig->display('user/add.html.twig',
        [
            'roles' => $roles,
        ]);

    }

    public function process($params = []) : void
    {
        $userId = $params['id'] ?? null;
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        if(isset($_POST["lastName"]))
        {
            if($userId !== null)
            {
                $userRepository = $entityManager->getRepository
                (User::class);
                $user = $userRepository->findById($userId);
                $user->setLastName($_POST["lastName"]);
                $user->setFirstName($_POST["firstName"]);
                $user->setEmailAddress($_POST["emailAdress"]);
                $user->setUsername($_POST["username"]);
                $user->setPassword($_POST["password"]);
                $roleRepository = $entityManager->getRepository(Role::class);
                $role = $roleRepository->findById($_POST["role"]);
                $user->setRole($role);
                $entityManager->flush();
            }
            else
            {
                $user = new User();
                $user->setLastName($_POST["lastName"]);
                $user->setFirstName($_POST["firstName"]);
                $user->setEmailAddress($_POST["emailAdress"]);
                $user->setUsername($_POST["username"]);
                $user->setPassword($_POST["password"]);
                $roleRepository = $entityManager->getRepository(Role::class);
                $role = $roleRepository->findById($_POST["role"]);
                $user->setRole($role);
                $entityManager->persist($user);
                $entityManager->flush();
            }
            $url .= "user";
        }
        else
        {
            $url .= "user/add";
        }
        header($url);
        exit();
    }

    public function modify(array $params) : void
    {
        $userId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);
        $roleRepository = $entityManager->getRepository(Role::class);
        $roles = $roleRepository->findAll();

        $this->twig->display('user/modify.html.twig',
            [
                'user' => $user,
                'roles' => $roles,
            ]);
    }

    public function delete(array $params) : void
    {
        $userId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findById($userId);
        $entityManager->remove($user);
        $entityManager->flush();
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        $url .= "user";
        header($url);
        exit();
    }
}