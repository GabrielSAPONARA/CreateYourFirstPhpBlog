<?php

namespace App\Router;

class RouteManager
{
    private $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function generatePath($name, $parameters = [])
    {
        $route = $this->findRouteByName($name);

        if (!$route) {
            throw new \InvalidArgumentException(sprintf('La route "%s" n\'existe pas.', $name));
        }

        $url = $route[1];
        foreach ($parameters as $key => $value) {
            $url = preg_replace('/\[(?:[^\:]+):' . preg_quote($key) . '\]/', $value, $url);
        }

        $url = preg_replace('/\[[^\]]+\]/', '', $url);


        return $url;
    }

    private function findRouteByName($name)
    {
        foreach ($this->router->getRoutes() as $route) {
//            dump($route);
            if (isset($route[3]) && $route[3] === $name) {
                return $route;
            }
        }

        return null;
    }
}
