<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Twig\Environment;
use App\Component\Session;
use App\Form\UserFormType;
use App\Form\MemberFormType;
use App\Router\RouteManager;
use App\Logger\LoggerManager;
use App\Form\UserRoleFormType;
use JetBrains\PhpStorm\NoReturn;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends BasicController
{

    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;
    private Session $session;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers,
        Session $session
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
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
        if($userId !== null)
        {
            $userRepository = $this->entityManager->getRepository
            (User::class);
            $user = $userRepository->findById($userId);
            $roleRepository = $this->entityManager->getRepository(Role::class);
            $role = $roleRepository->findById(filter_input(INPUT_POST,'Roles'));
            $user->setRole($role);
            $this->entityManager->flush();
            $userLogger->info("User " . $user->getId() . " has been modified.");
            $this->redirectToRoute('users');
        }
        else
        {
            if((filter_input(INPUT_POST,"Lastname",FILTER_SANITIZE_SPECIAL_CHARS) !== null) && (filter_input(INPUT_POST,"Firstname", FILTER_SANITIZE_SPECIAL_CHARS) !== null) && (filter_input(INPUT_POST,"Email_Address", FILTER_SANITIZE_SPECIAL_CHARS) !== null) && (filter_input(INPUT_POST,"Username", FILTER_SANITIZE_SPECIAL_CHARS) !== null) && (filter_input(INPUT_POST,"Password", FILTER_SANITIZE_SPECIAL_CHARS) !== null) && (filter_input(INPUT_POST,"Roles", FILTER_SANITIZE_SPECIAL_CHARS) !== null))
            {
                $user = new User();
                $user->setLastName(filter_input(INPUT_POST,'Lastname', FILTER_SANITIZE_SPECIAL_CHARS));
                $user->setFirstName(filter_input(INPUT_POST,'Firstname', FILTER_SANITIZE_SPECIAL_CHARS));
                $user->setEmailAddress(filter_input(INPUT_POST,'Email_Address', FILTER_SANITIZE_SPECIAL_CHARS));
                $user->setUsername(filter_input(INPUT_POST,'Username', FILTER_SANITIZE_SPECIAL_CHARS));
                $user->setPassword(filter_input(INPUT_POST,'Password', FILTER_SANITIZE_SPECIAL_CHARS));
                $roleRepository = $this->entityManager->getRepository(Role::class);
                $role = $roleRepository->findById(filter_input(INPUT_POST,'Roles', FILTER_SANITIZE_SPECIAL_CHARS));
                $user->setRole($role);
                $user->setIsActive(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $userLogger->info("New user added: " . $user->getId());
                $this->redirectToRoute('users');
            }
            else
            {
                $userLogger->warning("Some information about user are missing.");
                $this->redirectToRoute("users__addition");
            }
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

        $form = UserRoleFormType::buildForm($user, $roles);
        $this->twig->display('user/modify.html.twig',
            [
                'formFields' => $form->getFields(),
                'user' => $user,
                'roleIdOfUser' => $roleIdOfUser,
            ]);
    }

    #[NoReturn] public function delete(array $params) : void
    {
        $this->beforeAction('Administrator');
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

        if((filter_input(INPUT_POST,"Lastname") !== null) && (filter_input(INPUT_POST,"Firstname") !== null) && (filter_input(INPUT_POST,"Email_Address") !== null)  && (filter_input(INPUT_POST,"Username") !== null) && (filter_input(INPUT_POST,"Password") !== null))
        {
            $user = new User();
            $user->setLastName(filter_input(INPUT_POST,"Lastname", FILTER_SANITIZE_SPECIAL_CHARS));
            $user->setFirstName(filter_input(INPUT_POST,"Firstname", FILTER_SANITIZE_SPECIAL_CHARS));
            $user->setEmailAddress(filter_input(INPUT_POST,"Email_Address", FILTER_SANITIZE_SPECIAL_CHARS));
            $user->setUsername(filter_input(INPUT_POST,"Username", FILTER_SANITIZE_SPECIAL_CHARS));
            $user->setPassword(filter_input(INPUT_POST,"Password", FILTER_SANITIZE_SPECIAL_CHARS));
            $roleRepository = $this->entityManager->getRepository(Role::class);
            $role = $roleRepository->findByName("Member");
            $user->setRole($role);
            $user->setIsActive(true);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $userLogger->info("New user added: " . $user->getId());
            $route = "profile";
            $this->getSession()->set('user_id', $user->getId());
            $this->getSession()->set('username', $user->getUsername());
            $this->getSession()->set('role', $role->getName());
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
        $userId = $this->getSession()->get('user_id');
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findById($userId);

        $this->twig->display('user/profile.html.twig',
        [
            'user' => $user,
        ]);
    }

    public function modifyProfile() : void
    {
        $userLogger = $this->getLogger("user");
        $userId = $this->getSession()->get('user_id');
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findById($userId);

        $form = MemberFormType::buildForm($user);

        if(filter_input_array(INPUT_POST) !== null) {
            $form->bind(filter_input_array(INPUT_POST));

            if ($form->isValid())
            {
                $data = $form->getData();
                $user->setLastName($data["Lastname"]);
                $user->setFirstName($data["Firstname"]);
                $user->setEmailAddress($data["Email_Address"]);
                $user->setUsername($data["Username"]);
                if($data['Password'] !== $user->getPassword())
                {
                    $user->setPassword($data["Password"]);
                }

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $userLogger->info("User " . $user->getId() . " has been modified.");
                $route = "profile";
            }
            else
            {
                $route = "profile__modify";
            }
            $this->redirectToRoute($route);
        }
        else
        {
            $this->twig->display('user/modify_profile.html.twig',
            [
                'formFields' => $form->getFields(),
            ]);
        }
    }
}