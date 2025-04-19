<?php

define("ROOT", dirname(__DIR__));

use App\Router\RouteManager;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Twig\Environment;

require_once __DIR__ . '/../vendor/autoload.php';

// Manage errors with Whoops
$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();

// Load the configured container
$containerFactory = require __DIR__ . '/../config/services.php';
$container = $containerFactory();

$router = $container->get(RouteManager::class);
$match = $router->match();

$twig = $container->get(Environment::class);

function renderTemplate(Environment $twig, string $template, array $context = [], int $statusCode = 200)
{
    http_response_code($statusCode);
    echo $twig->render($template, $context);
}


if (is_array($match))
{
    list($controller, $action) = explode('::', $match['target']);
    $controllerClass = "App\\Controller\\" . $controller;

    if (class_exists($controllerClass))
    {
        try
        {
            $controller = $container->get($controllerClass);


            if (method_exists($controller, $action) && is_callable([$controller, $action]))
            {
                call_user_func_array([
                    $controller, $action
                ], [$match['params']]);
            }
            else
            {
                renderTemplate($twig, 'error/error404.html.twig', ['message' => "Action not callable."], 404);
            }
        }
        catch (Exception $e)
        {
            renderTemplate($twig, 'error/error500.html.twig', ['message' => $e->getMessage()], 500);
        }
    }
    else
    {
        renderTemplate($twig, 'error/error404.html.twig', ['message' => "Controller not found."], 404);
    }
}
else
{
    renderTemplate($twig, 'error/error404.html.twig', ['message' => "Route not matched."], 404);
}

