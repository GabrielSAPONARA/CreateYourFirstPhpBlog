<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleFormType;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class RoleController extends BasicController
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
        $this->beforeAction("Administrator");

        $roleRepository = $this->entityManager->getRepository(Role::class);
        $roles = $roleRepository->findAll();
        $this->twig->display('role/index.html.twig',
            [
                'roles' => $roles,
            ]);
    }

    public function add(): void
    {
        $this->beforeAction("Administrator");
        $form = RoleFormType::buildForm();
        $this->twig->display('role/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);

    }

    public function process($params = []) : void
    {
        $this->beforeAction("Administrator");
        $roleId = $params['id'] ?? null;
        $route = "";
        if(isset($_POST["name"]))
        {
            if($roleId !== null)
            {
                $roleRepository = $this->entityManager->getRepository
                (Role::class);
                $role = $roleRepository->findById($roleId);
                $role->setName($_POST["name"]);
                $this->entityManager->flush();
            }
            else
            {
                $role = new Role();
                $role->setName($_POST["name"]);
                $this->entityManager->persist($role);
                $this->entityManager->flush();
            }
            $route = "roles";
        }
        else
        {
            $route = "roles__addition";
        }
        $this->redirectToRoute($route);
    }

    public function modify(array $params) : void
    {
        $this->beforeAction("Administrator");
        $roleId = $params["id"];
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $role = $roleRepository->find($roleId);

        $form = RoleFormType::buildForm($role);

        $this->twig->display('role/modify.html.twig',
            [
                'role' => $role,
                'formFields' => $form->getFields(),
            ]);
    }

    public function delete(array $params) : void
    {
        $this->beforeAction("Administrator");
        $roleId = $params["id"];
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $role = $roleRepository->findById($roleId);
        $this->entityManager->remove($role);
        $this->entityManager->flush();
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        $url .= "role";
        header($url);
        exit();
    }
}