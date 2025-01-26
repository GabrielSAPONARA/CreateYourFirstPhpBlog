<?php

namespace App\Controller;

use App\Controller\BasicController;
use App\Entity\User;
use App\Form\LoginFormType;
use App\Logger\LoggerManager;
use JetBrains\PhpStorm\NoReturn;

class AuthController extends BasicController
{
    public function login() : void
    {
        $this->beforeAction("Disconnected user");
        $form = LoginFormType::buildForm();

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $form->bind($_POST);


            if($form->isValid())
            {
                $data = $form->getData();

                $entityManager = require_once __DIR__ . '/../../bootstrap.php';
                $authLogger = $this->getLogger('authentication');
                $userRepository = $entityManager->getRepository(User::class);
                $userArray = $userRepository->findByUsername($data['username']);
                if($userArray === null)
                {

                    $authLogger->warning("Incorrect authentication to username : " . $data['username']);
                    $error = "Incorrect Authentication";
                }
                else
                {
                    $user = $userArray[0];
                    if($user && \password_verify($data['password'], $user->getPassword()))
                    {
                        $this->setSession('user_id', $user->getId());
                        $this->setSession('username', $user->getUsername());
                        $this->setSession('role', $user->getRole()->getName());

                        session_regenerate_id(true);

                        $authLogger->info("Logged in user : " .
                                          $user->getUsername() . ", user id : " . $user->getId());
                        $this->redirectToRoute("posts");
                    }
                    else
                    {
                        $authLogger->warning("Incorrect authentication to username : " . $data['username']);
                        $error = "Incorrect Authentication";
                        $this->redirectToRoute("login");
                    }
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
        $authLogger = $this->getLogger('authentication');
        $authLogger->info("Logged out user : " . $this->getSession("username") .
                          ", user id : " .
                          $this->getSession("user_id"));
        $this->clearSession();
        $this->destroySession();

        $this->redirectToRoute("login");
    }
}