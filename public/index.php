<?php
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;

define("ROOT",dirname(__DIR__));
require_once __DIR__.'/../vendor/autoload.php';
$uri = $_SERVER['REQUEST_URI'];

//dump($uri);

$router = new AltoRouter();

require_once "../config/routes.php";
require_once "../src/Controller/WelcomeController.php";

$match = $router->match();

if(is_array($match))
{
    list($controller, $action) = explode('::', $match['target']);
    $controller = "App\\Controller\\".$controller;
    $controller = new $controller();
    if(is_callable(array($controller, $action)))
    {
        call_user_func_array(array($controller, $action), array($match['params']));
    }
    else
    {
        // $params = $match['params'];
        // ob_start();
        // dump($match['target']);
        // require "../templates/{$match['target']}.php";
        // $pageContent = ob_get_clean();
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

