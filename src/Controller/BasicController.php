<?php

namespace App\Controller;

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

    public function __construct(RouteManager $routerManager)
    {
        if(session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }

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
}
