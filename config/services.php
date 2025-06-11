<?php

use App\Component\Session;
use App\Controller\AuthController;
use App\Controller\BasicController;
use App\Controller\CommentController;
use App\Controller\ContactController;
use App\Controller\PostController;
use App\Controller\RoleController;
use App\Controller\UserController;
use App\Controller\WelcomeController;
use App\Service\CommentService;
use App\Service\PostService;
use App\Service\EmailService;
use App\Service\AuthService;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use App\Router\RouteManager;
use App\Logger\LoggerManager;
use Twig\TwigFunction;
use App\Bootstrap;

return function (): \DI\Container {
    $containerBuilder = new ContainerBuilder();

    $containerBuilder->useAutowiring(true);

    $bootstrap = new Bootstrap();
    $entityManager = $bootstrap->getEntityManager();

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
            ->method('addFunction', new TwigFunction('asset', function ($path) {
                return '/' . ltrim($path, '/');
            }))
            ->method('addFunction', new TwigFunction('path', function ($routeName, $parameters = []) use ($containerBuilder) {
                $routeManager = $containerBuilder->build()->get(RouteManager::class);
                return $routeManager->generatePath($routeName, $parameters);
            }))
            ->method('addFunction', new TwigFunction('is_granted', function ($roles) use ($containerBuilder) {
                $basicController = $containerBuilder->build()->get(BasicController::class);
                return $basicController->isGranted($roles);
            })),
    ]);

    // RouteManager
    $containerBuilder->addDefinitions([
        'router' => DI\factory(function () {
            $router = new AltoRouter();
            require __DIR__ . '/routes.php';
            return $router;
        }),
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
            'contact' => LoggerManager::getLogger('system_errors'),
        ],
    ]);

    $containerBuilder->addDefinitions([
        LoggerManager::class => DI\create(LoggerManager::class),
    ]);

    $containerBuilder->addDefinitions([
        Session::class => DI\autowire(Session::class),
    ]);

      // Services et controllers
    $containerBuilder->addDefinitions([
        PostService::class    => DI\autowire(PostService::class),
        CommentService::class => DI\autowire(CommentService::class),
        AuthService::class    => DI\autowire(AuthService::class),
        EsmtpTransport::class => function () {
            $transportFactory = new EsmtpTransportFactory();
            return $transportFactory->create(new Dsn(
                'smtp',
                'smtp.zoho.eu', // Nom du transport
                'gabriel.saponara@zohomail.eu',
                'Zm9D6Jkvxsqy',
                465,
            ));
        },
        Mailer::class => DI\create(Mailer::class)
            ->constructor(DI\get(EsmtpTransport::class)),
        EmailService::class => DI\autowire(EmailService::class)
            ->constructorParameter('mailer', DI\get(Mailer::class))
            ->constructorParameter('twig', DI\get(Environment::class)),
        PostController::class => DI\autowire(PostController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        WelcomeController::class => DI\autowire(WelcomeController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        AuthController::class => DI\autowire(AuthController::class)
            ->constructorParameter('loggers', DI\get('loggers')),// Passer le tableau des loggers
        CommentController::class => DI\autowire(CommentController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        RoleController::class => DI\autowire(RoleController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        SocialNetworkController::class => DI\autowire(SocialNetworkController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        UserController::class => DI\autowire(UserController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        ContactController::class => DI\autowire(ContactController::class)
            ->constructorParameter('loggers', DI\get('loggers')),
        BasicController::class => DI\autowire(BasicController::class)
            ->constructorParameter('session', DI\get(Session::class))
            ->constructorParameter('loggers', DI\get('loggers')),  //
        // Injection de l'objet LoggerManager
        // Injection de l'objet LoggerManager
    ]);



    return $containerBuilder->build();
};
