<?php

use App\Controller\PostController;
use App\Service\PostService;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use App\Router\RouteManager;
use App\Logger\LoggerManager;

return function (): \DI\Container {
    $containerBuilder = new ContainerBuilder();

    $containerBuilder->useAutowiring(true);

    $entityManager = require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
    // EntityManager setup
    $containerBuilder->addDefinitions([
        EntityManagerInterface::class => DI\value($entityManager),
    ]);

    // Twig's setup
    $containerBuilder->addDefinitions([
        FilesystemLoader::class => DI\create(FilesystemLoader::class)
            ->constructor(ROOT . '/templates'),
        Environment::class => DI\create(Environment::class)
            ->constructor(DI\get(FilesystemLoader::class), [
                'cache' => false,
                'debug' => true,
            ])
            ->method('addExtension', DI\get(DebugExtension::class))
            ->method('addFunction', DI\factory(function (RouteManager $routeManager) {
                return new Twig\TwigFunction('path', [$routeManager, 'generatePath']);
            }))

            ->method('addFunction', new Twig\TwigFunction('asset', function ($path) {
                return '/' . ltrim($path, '/');
            }))

    ]);

    // RouteManager
    $containerBuilder->addDefinitions([
        // Instancier le routeur AltoRouter
        'router' => DI\factory(function () {
            $router = new AltoRouter();

            // Charger le fichier contenant les définitions des routes
            require __DIR__ . '/routes.php';

            return $router;
        }),

        // RouteManager qui dépend du routeur
        RouteManager::class => DI\create(RouteManager::class)
            ->constructor(DI\get('router')),
    ]);

    // Loggers
    $containerBuilder->addDefinitions([
        'loggers' => [
            'app' => LoggerManager::getLogger('app'),
            'user' => LoggerManager::getLogger('user'),
            'authentication' => LoggerManager::getLogger('authentication'),
            'database' => LoggerManager::getLogger('database'),
            'post' => LoggerManager::getLogger('post_management'),
            'comment' => LoggerManager::getLogger('comment_management'),
            'admin' => LoggerManager::getLogger('admin_actions'),
            'system' => LoggerManager::getLogger('system_errors'),
        ],
    ]);

    $containerBuilder->addDefinitions([
        PostService::class => DI\autowire(PostService::class),
    ]);

    $containerBuilder->addDefinitions([
        PostController::class => DI\autowire(PostController::class)
            ->constructorParameter('loggers', DI\get('loggers'))
    ]);

    return $containerBuilder->build();
};
