<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\UserFormType;
use App\Logger\LoggerManager;
use JetBrains\PhpStorm\NoReturn;

class UserController extends BasicController
{
    public function index(): void
    {
        $this->beforeAction('Administrator');

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

        $form = UserFormType::buildForm(null, $roles);
        $this->twig->display('user/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);

    }

    #[NoReturn] public function process($params = []) : void
    {
        $userId = $params['id'] ?? null;
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';

        $userLogger = $this->getLogger("user");
        if((isset($_POST["Last_Name"])) && (isset($_POST["First_Name"])) &&
           (isset($_POST["Email_Adress"]))  && (isset($_POST["Username"])) &&
           (isset($_POST["Password"])) && (isset($_POST["Roles"])))
        {
            if($userId !== null)
            {
                $userRepository = $entityManager->getRepository
                (User::class);
                $user = $userRepository->findById($userId);
                $user->setLastName($_POST["Last_Name"]);
                $user->setFirstName($_POST["First_Name"]);
                $user->setEmailAddress($_POST["Email_Adress"]);
                $user->setUsername($_POST["Username"]);
                $user->setPassword($_POST["Password"]);
                $roleRepository = $entityManager->getRepository(Role::class);
                $role = $roleRepository->findById($_POST["Roles"]);
                $user->setRole($role);
                $entityManager->flush();
                $userLogger->info("User " . $user->getId() . " has been modified.");
            }
            else
            {
                $user = new User();
                $user->setLastName($_POST["Last_Name"]);
                $user->setFirstName($_POST["First_Name"]);
                $user->setEmailAddress($_POST["Email_Adress"]);
                $user->setUsername($_POST["Username"]);
                $user->setPassword($_POST["Password"]);
                $roleRepository = $entityManager->getRepository(Role::class);
                $role = $roleRepository->findById($_POST["Roles"]);
                $user->setRole($role);
                $entityManager->persist($user);
                $entityManager->flush();
                $userLogger->info("New user added: " . $user->getId());
            }
            $this->redirectToRoute('users');
        }
        else
        {
            $userLogger->warning("Some information about user are missing.");
            $this->redirectToRoute("users__addition");
        }
    }

    public function modify(array $params) : void
    {
        $userId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);
        $roleRepository = $entityManager->getRepository(Role::class);
        $roles = $roleRepository->findAll();
        $roleIdOfUser = $user->getRole()->getId();

        $form = UserFormType::buildForm($user, $roles);
        $this->twig->display('user/modify.html.twig',
            [
                'formFields' => $form->getFields(),
                'user' => $user,
                'roleIdOfUser' => $roleIdOfUser,
            ]);
    }

    #[NoReturn] public function delete(array $params) : void
    {
        $userId = $params["id"];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findById($userId);
        $userLogger = $this->getLogger('user');
        $userLogger->warning("User " . $user->getId() . " has been deleted.");
        $entityManager->remove($user);
        $entityManager->flush();
        $this->redirectToRoute('users');
    }
}