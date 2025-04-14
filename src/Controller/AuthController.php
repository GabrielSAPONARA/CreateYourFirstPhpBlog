<?php

namespace App\Controller;

use App\Controller\BasicController;
use App\Entity\User;
use App\Form\LoginFormType;
use App\Logger\LoggerManager;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Twig\Environment;

class AuthController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers
    )
    {
        parent::__construct($twig, $routeManager, $loggers);
        $this->entityManager = $entityManager;
    }
    public function login() : void
    {
        $this->beforeAction("Disconnected user");
        $form = LoginFormType::buildForm();

        if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST')
        {
            $form->bind(filter_input_array(INPUT_POST));


            if($form->isValid())
            {
                $data = $form->getData();

                $authLogger = $this->getLogger('authentication');
                $userRepository = $this->entityManager->getRepository(User::class);
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
            'formFields' => $form->getFields(),
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

        $this->redirectToRoute("welcome");
    }
}