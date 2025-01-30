<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\MemberFormType;
use App\Form\UserFormType;
use App\Logger\LoggerManager;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Twig\Environment;

class UserController extends BasicController
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

    public function index(): void
    {
        $this->beforeAction('Administrator');

        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();
        $this->twig->display('user/index.html.twig',
            [
                'users' => $users,
            ]);
    }

    public function add(): void
    {
        $this->beforeAction('Administrator');
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $roles = $roleRepository->findAll();

        $form = UserFormType::buildForm(null, $roles);
        $this->twig->display('user/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);

    }

    #[NoReturn] public function process($params = []) : void
    {
        $this->beforeAction('Administrator');
        $userId = $params['id'] ?? null;

        $userLogger = $this->getLogger("user");
        if((isset($_POST["Lastname"])) && (isset($_POST["Firstname"])) &&
           (isset($_POST["Email_Address"]))  && (isset($_POST["Username"])) &&
           (isset($_POST["Password"])) && (isset($_POST["Roles"])))
        {
            if($userId !== null)
            {
                $userRepository = $this->entityManager->getRepository
                (User::class);
                $user = $userRepository->findById($userId);
                $user->setLastName($_POST["Lastname"]);
                $user->setFirstName($_POST["Firstname"]);
                $user->setEmailAddress($_POST["Email_Address"]);
                $user->setUsername($_POST["Username"]);
                $user->setPassword($_POST["Password"]);
                $roleRepository = $this->entityManager->getRepository(Role::class);
                $role = $roleRepository->findById($_POST["Roles"]);
                $user->setRole($role);
                $this->entityManager->flush();
                $userLogger->info("User " . $user->getId() . " has been modified.");
            }
            else
            {
                $user = new User();
                $user->setLastName($_POST["Lastname"]);
                $user->setFirstName($_POST["Firstname"]);
                $user->setEmailAddress($_POST["Email_Address"]);
                $user->setUsername($_POST["Username"]);
                $user->setPassword($_POST["Password"]);
                $roleRepository = $this->entityManager->getRepository(Role::class);
                $role = $roleRepository->findById($_POST["Roles"]);
                $user->setRole($role);
                $user->setIsActive(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
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
        $this->beforeAction('Administrator');
        $userId = $params["id"];
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);
        $roleRepository = $this->entityManager->getRepository(Role::class);
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
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findById($userId);
        $userLogger = $this->getLogger('user');
        $userLogger->warning("User " . $user->getId() . " has been deleted.");
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->redirectToRoute('users');
    }

    public function register() : void
    {
        $form = MemberFormType::buildForm();
        $this->twig->display('user/register.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);
    }

    public function processToRegister()
    {
        $userLogger = $this->getLogger("user");
        $route = "";

        if((isset($_POST["Lastname"])) && (isset($_POST["Firstname"])) &&
           (isset($_POST["Email_Address"]))  && (isset($_POST["Username"])) &&
           (isset($_POST["Password"])))
        {
            $user = new User();
            $user->setLastName($_POST["Lastname"]);
            $user->setFirstName($_POST["Firstname"]);
            $user->setEmailAddress($_POST["Email_Address"]);
            $user->setUsername($_POST["Username"]);
            $user->setPassword($_POST["Password"]);
            $roleRepository = $this->entityManager->getRepository(Role::class);
            $role = $roleRepository->findByName("Member");
            $user->setRole($role);
            $user->setIsActive(true);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $userLogger->info("New user added: " . $user->getId());
            $route = "welcome";
            $this->setSession('user_id', $user->getId());
            $this->setSession('username', $user->getUsername());
            $this->setSession('role', $role->getName());
        }
        else
        {
            $userLogger->warning("Some information about user are missing.");
            $route = "register";
        }
        $this->redirectToRoute($route);
    }

    public function profile()
    {
        $userId = $this->getSession('user_id');
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findById($userId);

        $this->twig->display('user/profile.html.twig',
        [
            'user' => $user,
        ]);
    }
}