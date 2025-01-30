<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Ramsey\Uuid\Uuid;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Service\PostService;

class PostController extends BasicController
{
    private PostService $postService;
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;


    public function __construct
    (
        PostService $postService,
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers
    )
    {
        parent::__construct($twig, $routeManager, $loggers);
        $this->entityManager = $entityManager;
        $this->postService = $postService;
    }
    public function index() : void
    {
        $posts = $this->postService->getAllPosts();

        $this->twig->display('post/index.html.twig',
            [
                'posts' => $posts
            ]);
    }

    public function add() : void
    {
        $this->beforeAction("Member");
        $form = PostFormType::buildForm();

        $this->twig->display('post/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);
    }

    #[NoReturn] public function process($params =
    []) : void
    {
        $this->beforeAction("Member");

        $postId = $params['id'] ?? null;
        $currentUser = $this->entityManager->getRepository(User::class)->findById
        ($this->getSession("user_id"));


        try
        {
            $this->postService->savePost($_POST, $postId, $currentUser);
            $route = "posts";
        }
        catch (\Exception $exception)
        {
            $this->getLogger('post')->error("Error processing post form: " . $e->getMessage());
            $route = "posts_addition";
        }

        $this->redirectToRoute($route);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[NoReturn] public function details (array $params) : void
    {
        $uuid = $params["postId"];
        $postRepository = $this->entityManager->getRepository(Post::class);
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findByPost($uuid);
        $post = $postRepository->findById($uuid)[0];
        $author = $post->getUser();

        $this->twig->display('post/detail.html.twig',
        [
            'post' => $post,
            'author' => $author,
            'comments' => $comments
        ]);
    }
}