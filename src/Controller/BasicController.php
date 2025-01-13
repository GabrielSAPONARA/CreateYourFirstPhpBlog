<?php

namespace App\Controller;

use App\Logger\LoggerManager;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use App\Router\RouteManager;
use \Twig\Extension\DebugExtension;

class BasicController
{
    private $loader;
    protected $twig;
    private $routerManager;
    protected array $loggers;

    private array $roleHierarchy =
    [
        'Administrator' => ['Moderator', 'Member', 'Disconnected user'],
        'Moderator' => ['Member', 'Disconnected user'],
        'Member' => ['Disconnected user'],
        'Disconnected user' => [],
    ];

    public function __construct(RouteManager $routerManager)
    {
        if(session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
        session_regenerate_id(true);

        try
        {
            $this->loader = new FilesystemLoader(ROOT.'/templates');

            $this->twig = new Environment($this->loader, [
                'cache' => false,
                'debug' => true,
            ]);
        }
        catch (\Exception $e)
        {
            dump($e->getMessage());
        }

        $this->routerManager = $routerManager;

        $this->twig->addFunction(new TwigFunction('asset', function ($path) {
            return "/" . ltrim($path, '/');
        }));
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());

        $this->twig->addFunction(new TwigFunction('path',
            [$this->routerManager, 'generatePath']));

        $this->loggers =
        [
            'app' => LoggerManager::getLogger('app'),
            'user' => LoggerManager::getLogger('user'),
            'authentication' => LoggerManager::getLogger('authentication'),
            'database' => LoggerManager::getLogger('database'),
            'post' => LoggerManager::getLogger('post_management'),
            'comment' => LoggerManager::getLogger('comment_management'),
            'admin' => LoggerManager::getLogger('admin_actions'),
            'system' => LoggerManager::getLogger('system_errors'),
        ];
    }

    #[NoReturn] protected function redirectToRoute(string $routeName, array $parameters = []) : RedirectResponse
    {
        $url = $this->routerManager->generatePath($routeName, $parameters);
        header("Location: $url");
        exit();
    }

    protected function setSession($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    protected function getSession(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    protected function destroySession(): void
    {
        session_destroy();
    }

    protected function clearSession(): void
    {
        $_SESSION = [];
    }

    protected function getAllRoles(string $role): array
    {
        $roles = [$role];

        if (isset($this->roleHierarchy[$role])) {
            foreach ($this->roleHierarchy[$role] as $childRole) {
                $roles = array_merge($roles, $this->getAllRoles($childRole));
            }
        }

        return array_unique($roles);
    }

    protected function checkAuth(string $requiredRole = null): void
    {
        if (!$this->getSession('user_id') && $requiredRole !== 'Disconnected user')
        {
            $this->redirectToRoute('login');
        }
    }

    protected function isGranted(array|string $roles): bool
    {
        $userRole = $this->getSession('role') ?? 'Disconnected user';

//        dd($userRole);
        $allRoles = $this->getAllRoles($userRole);

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return !empty(array_intersect($roles, $allRoles));
    }

    protected function beforeAction(string $requiredRole = null): void
    {
        $this->checkAuth($requiredRole);

        if ($requiredRole && !$this->isGranted($requiredRole))
        {
            $this->redirectToRoute('forbidden');
        }
    }

    protected function getLogger(string $name)
    {
        return $this->loggers[$name] ?? null;
    }

    public function forbidden(): void
    {
        $this->twig->display('error/error403.html.twig', [
            'message' => 'Vous n’avez pas les permissions nécessaires pour accéder à cette page.',
        ]);
    }
}
