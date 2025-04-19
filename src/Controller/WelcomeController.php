<?php

namespace App\Controller;

use App\Component\Session;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class WelcomeController extends BasicController
{
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;
    private Session $session;

    public function __construct
    (
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers,
        Session $session
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(): void
    {
        $this->twig->display('welcome/index.html.twig');
    }
}