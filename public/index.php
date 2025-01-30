<?php

define("ROOT", dirname(__DIR__));

use App\Router\RouteManager;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

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

//dump($_SERVER["REQUEST_URI"]);
//dump($router);
//dump($match);

if (is_array($match))
{
    list($controller, $action) = explode('::', $match['target']);
    $controllerClass = "App\\Controller\\" . $controller;

    if (class_exists($controllerClass))
    {
        try
        {
            $controller = $container->get($controllerClass);


            if (is_callable([$controller, $action]))
            {
                call_user_func_array([
                    $controller, $action
                ], [$match['params']]);
            }
            else
            {
                echo "404 Not Found - Action not callable.";
            }
        }
        catch (Exception $e)
        {
            echo "500 Internal Server Error - " . $e->getMessage();
        }
    }
    else
    {
        echo "404 Not Found - Controller not found.";
    }
}
else
{
    echo "404 Not Found - Route not matched.";
}

