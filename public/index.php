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

function outputMessage(string $message, int $statusCode = 200)
{
    http_response_code($statusCode);
    return htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
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
                print outputMessage("404 Not Found - Action not callable.",
                    404);
            }
        }
        catch (Exception $e)
        {
            print outputMessage("500 Internal Server Error - " .
                               $e->getMessage(),
                500);
        }
    }
    else
    {
        print outputMessage("404 Not Found - Controller not found.", 404);
    }
}
else
{
    print outputMessage("404 Not Found - Route not matched.", 404);
}

