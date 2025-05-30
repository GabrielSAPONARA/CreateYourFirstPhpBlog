<?php

namespace App\Controller;

use App\Component\Session;
use App\Entity\Role;
use App\Form\RoleFormType;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

class RoleController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;
    private Session $session;


    /**
     * @param EntityManagerInterface $entityManager
     * @param Environment $twig
     * @param RouteManager $routeManager
     * @param array $loggers
     * @param Session $session
     */
    public function __construct
    (
        EntityManagerInterface   $entityManager,
        \Twig\Environment        $twig,
        \App\Router\RouteManager $routeManager,
        array                    $loggers,
        Session                  $session
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
        $this->entityManager = $entityManager;
    }

    /**
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
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

    /**
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add(): void
    {
        $this->beforeAction("Administrator");
        $form = RoleFormType::buildForm();
        $this->twig->display('role/add.html.twig',
            [
                'formFields' => $form->getFields(),
            ]);

    }

    /**
     * @param $params
     * @return void
     */
    public function process($params = []): void
    {
        $this->beforeAction("Administrator");
        $roleLogger = $this->getLogger("role");
        $roleId = $params['id'] ?? null;
        $route = "";
        if ((filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS) !==
             null))
        {
            if ($roleId !== null)
            {
                $roleRepository = $this->entityManager->getRepository
                (Role::class);
                $role = $roleRepository->findById($roleId);
                $role->setName(filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS));
                $this->entityManager->flush();

                $roleLogger->warning("Role " . $role->getName() .
                                     " was updated.");
            }
            else
            {
                $role = new Role();
                $role->setName(filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS));
                $this->entityManager->persist($role);
                $this->entityManager->flush();

                $roleLogger->warning("Role " . $role->getName() .
                                     " was created.");
            }
            $route = "roles";
        }
        else
        {
            $route = "roles__addition";
        }
        $this->redirectToRoute($route);
    }

    /**
     * @param array $params
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function modify(array $params): void
    {
        $this->beforeAction("Administrator");
        $roleId = $params["id"];
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $role = $roleRepository->find($roleId);

        $form = RoleFormType::buildForm($role);

        $this->twig->display('role/modify.html.twig',
            [
                'role'       => $role,
                'formFields' => $form->getFields(),
            ]);
    }

    /**
     * @param array $params
     * @return void
     */
    public function delete(array $params): void
    {
        $this->beforeAction("Administrator");
        $roleId = $params["id"];
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $role = $roleRepository->findById($roleId);
        $this->entityManager->remove($role);
        $this->entityManager->flush();

        $roleLogger = $this->getLogger("role");

        $roleLogger->warning("Role " . $role->getName() . " was removed.");
        $this->redirectToRoute("roles");
    }
}