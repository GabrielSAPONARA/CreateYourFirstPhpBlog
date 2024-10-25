<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use App\Router\RouteManager;

class BasicController
{
    private $loader;
    protected $twig;
    private $routerManager;

    public function __construct(RouteManager $routerManager)
    {
        try
        {
            $this->loader = new FilesystemLoader(ROOT.'/templates');

            $this->twig = new Environment($this->loader, [
                'cache' => false,
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

        $this->twig->addFunction(new TwigFunction('path',
            [$this->routerManager, 'generatePath']));
    }
}
