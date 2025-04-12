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

    public function __construct(Environment $twig, RouteManager $routerManager, array $loggers)
    {
        ob_start();
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '', // Change if you have a domain name
                'secure' => false,
//                'secure' => isset($_SERVER['HTTPS']), // Have to true if you use HTTPS
                'httponly' => true,
                'samesite' => 'Lax' // Put 'None' if you need and if you use HTTPS
            ]);
            session_start();
        }
        session_regenerate_id(true);

        $this->twig = $twig;
        $this->routerManager = $routerManager;
        $this->loggers = $loggers;
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

    protected function removeSessionValue(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
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

    protected function addFlashMessage(string $type, string $message, $duration = 5): void
    {
        if ($this->getSession('flash_messages') === null)
        {
            $this->setSession('flash_messages', []);
        }
        $_SESSION['flash_messages'][$type][] =
            [
                'message' => $message,
                'expiresAt' => $duration
            ];
    }

    protected function clearFlashMessages(): array
    {
        $now = time();
        foreach ($this->getSession('flash_messages') as $type => $messages)
        {
            foreach ($messages as $index => $msg)
            {
                if ($msg['expiresAt'] < $now)
                {
                    unset($this->getSession('flash_messages')[$type][$index]);
                }
            }
            if (empty($this->getSession('flash_messages')[$type]))
            {
                unset($this->getSession('flash_messages')[$type]);
            }
        }


        $flashMessages = $this->getSession('flash_messages') ?? [];
        unset($_SESSION['flash_messages']);

        return $flashMessages;
    }

    protected function getFlashMessages(): array
    {
        if (!isset($_SESSION['flash_messages']))
        {
            return [];
        }

        return $this->clearFlashMessages();
    }

    protected function removeFlashMessage(int $index): void
    {
        if (isset($this->getSession('flash_messages')[$index])) {
            unset($this->getSession('flash_messages')[$index]);
            $this->setSession('flash_messages', array_values($this->getSession('flash_messages')));
        }
    }

    protected function render(string $template, array $data = []): void
    {
        $data['flash_messages'] = $this->getFlashMessages();
        $this->twig->display($template, $data);

        $this->removeSessionValue('flash_messages');
    }
}
