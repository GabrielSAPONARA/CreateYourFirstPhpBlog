<?php

namespace App\Controller;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment;
use App\Router\RouteManager;
use App\Component\Session;

class BasicController
{
    protected Environment $twig;
    private RouteManager $routerManager;
    protected array $loggers;
    private Session $session;

    private array $roleHierarchy = [
        'Administrator' => ['Moderator', 'Member', 'Disconnected user'],
        'Moderator' => ['Member', 'Disconnected user'],
        'Member' => ['Disconnected user'],
        'Disconnected user' => [],
    ];

    public function __construct(Environment $twig, RouteManager $routerManager, array $loggers, Session $session)
    {
        ob_start();
        $this->twig = $twig;
        $this->routerManager = $routerManager;
        $this->loggers = $loggers;
        $this->session = $session;
    }

    protected function redirectToRoute(string $routeName, array $parameters = []): RedirectResponse
    {
        $url = $this->routerManager->generatePath($routeName, $parameters);
        header("Location: $url");
        exit();
    }

    protected function checkAuth(null|string $requiredRole): void
    {
        if (!$this->session->get('user_id') && $requiredRole !== 'Disconnected user') {
            $this->redirectToRoute('login');
        }
    }

    public function isGranted(array|string $roles): bool
    {
        $userRole = $this->session->get('role') ?? 'Disconnected user';
        $allRoles = $this->getAllRoles($userRole);

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return !empty(array_intersect($roles, $allRoles));
    }

    protected function beforeAction(null|string $requiredRole): void
    {
        $this->checkAuth($requiredRole);

        if ($requiredRole && !$this->isGranted($requiredRole)) {
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

    protected function render(string $template, array $data = []): void
    {
        $data['flash_messages'] = $this->session->getFlashMessages();
        $this->twig->display($template, $data);

        $this->session->remove('flash_messages');
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

    public function getSession(): Session
    {
        return $this->session;
    }

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }
}
