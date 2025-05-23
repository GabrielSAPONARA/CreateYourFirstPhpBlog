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

    public function generatePath(string $routeName, array $parameters = []): string
    {
        $route = $this->findRouteByName($routeName);

        if (!$route) {
            throw new \InvalidArgumentException("Route with name '{$routeName}' not found.");
        }

        $url = $route[1];

        foreach ($parameters as $key => $value) {
            $url = preg_replace('/\[' . preg_quote("uuid:$key", '/') . '\]/',
                $value, $url);
        }

        $queryString = http_build_query(array_diff_key($parameters, array_flip(array_keys($route['params'] ?? []))));

        return $url . ($queryString ? '?' . $queryString : '');
    }


    private function findRouteByName($name)
    {
        foreach ($this->router->getRoutes() as $route) {
            if ($route[3] === $name) {
                return $route;
            }
        }

        return null;
    }

    public function match(): bool|array
    {
        return $this->router->match();
    }
}

