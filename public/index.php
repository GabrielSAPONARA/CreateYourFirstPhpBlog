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

// Router's setup
$router = new AltoRouter();
require_once __DIR__ . '/../config/routes.php';
$routeManager = new RouteManager($router);

$match = $router->match();

if (is_array($match)) {
    list($controller, $action) = explode('::', $match['target']);
    $controllerClass = "App\\Controller\\" . $controller;

    if (class_exists($controllerClass)) {
        try {
            $controller = $container->get($controllerClass);


            if (is_callable([$controller, $action])) {
                call_user_func_array([
                    $controller, $action
                ], [$match['params']]);
            }
            else {
                echo "404 Not Found - Action not callable.";
            }
        }
        catch (Exception $e) {
            echo "500 Internal Server Error - " . $e->getMessage();
        }
    }
    else {
        echo "404 Not Found - Controller not found.";
    }
}
else {
    echo "404 Not Found - Route not matched.";
}




//define("ROOT",dirname(__DIR__));
//
//use App\Router\RouteManager;
//use Whoops\Handler\PrettyPageHandler;
//use Whoops\Run;
//use DI\Container;
//use App\Service\PostService;
//use Doctrine\ORM\EntityManagerInterface;
//
//
//require_once __DIR__. DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'vendor'. DIRECTORY_SEPARATOR .'autoload.php';
//
//$whoops = new Run;
//$whoops->pushHandler(new PrettyPageHandler);
//$whoops->register();
//
//$uri = $_SERVER['REQUEST_URI'];
//
////dump($uri);
//
//$router = new AltoRouter();
//require_once "..". DIRECTORY_SEPARATOR ."config". DIRECTORY_SEPARATOR ."routes.php";
//$routeManager = new RouteManager($router);
//
//
//$match = $router->match();
//
//$container = new Container();
//
//$entityManager = require_once  ".." . DIRECTORY_SEPARATOR . "config" .
//                 DIRECTORY_SEPARATOR . "bootstrap.php";
//
//$container->set(EntityManagerInterface::class, $entityManager);
//$container->set(PostService::class, DI\autowire(PostService::class));
//
//if(is_array($match))
//{
//    list($controller, $action) = explode('::', $match['target']);
//    $controllerClass = "App\\Controller\\".$controller;
////    $controller = new $controller($routeManager);
//
//    if(class_exists($controller))
//    {
//        $controller = $container->get($controllerClass);
//        if(is_callable(array($controller, $action)))
//        {
//            call_user_func_array(array($controller, $action), array($match['params']));
//        }
//        else
//        {
//            echo "404 Not Found";
//        }
//    }
//    else
//    {
//        echo "404 Not Found";
//    }
//
//}
//else
//{
//    echo "404 Not Found";
//}

//dump($_SERVER);
//dump($_SERVER['REQUEST_URI']);
//dump($_SERVER['PATH_INFO']);

//$request = Request::createFromGlobals();
//$path = $request->getPathInfo();
//dump($path);

