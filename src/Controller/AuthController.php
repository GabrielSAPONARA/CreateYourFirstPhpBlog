<?php

namespace App\Controller;

use App\Component\Session;
use App\Controller\BasicController;
use App\Entity\User;
use App\Form\LoginFormType;
use App\Logger\LoggerManager;
use App\Router\RouteManager;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Twig\Environment;

class AuthController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;

    private AuthService $authService;


    /**
     * @param EntityManagerInterface $entityManager
     * @param Environment $twig
     * @param RouteManager $routeManager
     * @param array $loggers
     * @param Session $session
     * @param AuthService $authService
     */
    public function __construct
    (
        EntityManagerInterface   $entityManager,
        \Twig\Environment        $twig,
        \App\Router\RouteManager $routeManager,
        array                    $loggers,
        Session                  $session,
        AuthService              $authService
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * @return void
     * @throws \Random\RandomException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function login(): void
    {
        $form = LoginFormType::buildForm();

        if (empty($this->getSession()->getSess()))
        {
            if (filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !==
                null)
            {
                $form->bind(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));

                if ($form->isValid())
                {
                    $data = $form->getData();
                    $authLogger = $this->getLogger('authentication');
                    $userRepository = $this->entityManager->getRepository(User::class);
                    $userArray = $userRepository->findByUsername($data['username']);
                    if ($userArray === null)
                    {

                        $authLogger->warning("Incorrect authentication to username : " .
                                             $data['username']);
                        $error = "Incorrect Authentication";
                    }
                    else
                    {

                        $user = $userArray[0];

                        if ($user &&
                            \password_verify($data['password'], $user->getPassword()))
                        {
                            $this->authService->updateSessionWithCurrentUser($user);

                            $authLogger->info("Logged in user : " .
                                              $user->getUsername() .
                                              ", user id : " . $user->getId());
                            $this->redirectToRoute("posts");
                        }
                        else
                        {
                            $authLogger->warning("Incorrect authentication to username : " .
                                                 $data['username']);
                            $error = "Incorrect Authentication";
                            $this->redirectToRoute("login");
                        }
                    }

                }
            }
        }
        else
        {
            $this->redirectToRoute("posts");
        }

        $this->twig->display('auth/login.html.twig',
            [
                'formFields' => $form->getFields(),
                'error'      => $error ?? null,
            ]);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $authLogger = $this->getLogger('authentication');
        $authLogger->info("Logged out user : " .
                          $this->getSession()->get("username") .
                          ", user id : " . $this->getSession()->get("user_id"));
        $this->getSession()->clear();
        $this->getSession()->destroy();

        $this->redirectToRoute("welcome");
    }


}