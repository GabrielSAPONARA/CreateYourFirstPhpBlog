<?php

namespace App\Router;

use AltoRouter;

class RouteManager
{
    private AltoRouter $router;

    public function __construct(AltoRouter $router)
    {
        $this->router = $router;
    }

    public function generatePath($name, $parameters = [])
    {
        $route = $this->findRouteByName($name);

        if (!$route) {
            throw new \InvalidArgumentException(sprintf('The route "%s" is not defined.', $name));
        }

        $url = $route['target'];
        foreach ($parameters as $key => $value) {
            $url = preg_replace('/\[' . preg_quote($key, '/') . '\]/', $value, $url);
        }

        return $url;
    }

    private function findRouteByName($name)
    {
        foreach ($this->router->getRoutes() as $route) {
            if ($route['name'] === $name) {
                return $route;
            }
        }

        return null;
    }
}

