<?php

namespace App\Controller;

use App\Controller\BasicController;
use App\Entity\User;
use App\Form\LoginFormType;
use JetBrains\PhpStorm\NoReturn;

class AuthController extends BasicController
{
    public function login() : void
    {
        $this->checkAuth();

        $form = LoginFormType::buildForm();

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $form->bind($_POST);


            if($form->isValid())
            {
//                dump("bonjour");
//                dd($_POST);
                $data = $form->getData();

                $entityManager = require_once __DIR__ . '/../../bootstrap.php';

                $userRepository = $entityManager->getRepository(User::class);
                $userArray = $userRepository->findByUsername($data['username']);
                if($userArray === null)
                {

                }

                if($user && \password_verify($data['password'], $user->getPassword()))
                {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['role'] = $user->getRole();

                    session_regenerate_id(true);

                    $this->redirectToRoute("users");
                }
                else
                {
                    $error = "Incorrect Authentication";
                    $this->redirectToRoute("login");
                }
            }
        }

        $this->twig->display('auth/login.html.twig',
        [
            'form' => $form->getFields(),
            'error' => $error ?? null,
        ]);
    }

    #[NoReturn] public function logout() : void
    {
        $this->clearSession();
        $this->destroySession();

        $this->redirectToRoute("login");
    }
}