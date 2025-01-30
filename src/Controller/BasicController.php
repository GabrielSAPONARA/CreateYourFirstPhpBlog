<?php

namespace App\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment;
use App\Router\RouteManager;

class BasicController
{
    protected Environment $twig;
    private RouteManager $routerManager;
    protected array $loggers;

    private array $roleHierarchy =
        [
            'Administrator' => ['Moderator', 'Member', 'Disconnected user'],
            'Moderator' => ['Member', 'Disconnected user'],
            'Member' => ['Disconnected user'],
            'Disconnected user' => [],
        ];

    protected string|null $currentRoute;

    protected string|null $previousRoute;

    public function __construct(Environment $twig, RouteManager $routerManager, array $loggers)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);

        $currentRoute = $_SERVER['REQUEST_URI'] ?? '/';
        $previousRoute = $_SESSION['previous_route'] ?? null;

        $this->setCurrentRoute($currentRoute);
        $this->setPreviousRoute($previousRoute);

        $this->twig = $twig; // Twig est maintenant injecté et déjà configuré
        $this->routerManager = $routerManager;
        $this->loggers = $loggers; // Injecté pour éviter la redondance
    }

    #[NoReturn]
    protected function redirectToRoute(string $routeName, array $parameters = []): RedirectResponse
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
        if (!$this->getSession('user_id') && $requiredRole !== 'Disconnected user') {
            $this->redirectToRoute('login');
        }
    }

    public function isGranted(array|string $roles): bool
    {
        $userRole = $this->getSession('role') ?? 'Disconnected user';
        $allRoles = $this->getAllRoles($userRole);

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return !empty(array_intersect($roles, $allRoles));
    }

    protected function beforeAction(string $requiredRole = null): void
    {
        $this->checkAuth($requiredRole);

        if ($requiredRole && !$this->isGranted($requiredRole)) {
            $this->redirectToRoute('forbidden');
        }
    }

    protected function getCurrentRoute(): ?string
    {
        return $_SERVER['REQUEST_URI'] ?? null;
    }

    public function setCurrentRoute(?string $currentRoute): void
    {
        $this->currentRoute = $currentRoute;
        $_SESSION['current_route'] = $currentRoute;
    }

    protected function getPreviousRoute(): ?string
    {
        return $_SESSION['previous_route'] ?? null;
    }

    public function setPreviousRoute(?string $previousRoute): void
    {
        $this->previousRoute = $previousRoute;
        $_SESSION['previous_route'] = $previousRoute;
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
