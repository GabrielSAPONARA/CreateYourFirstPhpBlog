<?php

define("ROOT",dirname(__DIR__));

use App\Router\RouteManager;

require_once __DIR__.'/../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];

//dump($uri);

$router = new AltoRouter();
require_once "../config/routes.php";
$routeManager = new RouteManager($router);


$match = $router->match();
//dump($match);

if(is_array($match))
{
    list($controller, $action) = explode('::', $match['target']);
    $controller = "App\\Controller\\".$controller;
    $controller = new $controller($routeManager);
    if(is_callable(array($controller, $action)))
    {
        call_user_func_array(array($controller, $action), array($match['params']));
    }
    else
    {
        echo "404 Not Found";
    }
}
else
{
    echo "404 Not Found";
}

//dump($_SERVER);
//dump($_SERVER['REQUEST_URI']);
//dump($_SERVER['PATH_INFO']);

//$request = Request::createFromGlobals();
//$path = $request->getPathInfo();
//dump($path);

