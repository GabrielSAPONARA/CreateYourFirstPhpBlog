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

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $form->bind($_POST);
            $route = "";
            $routeParams = [];


            if ($form->isValid())
            {
                $userId = $this->getSession("user_id");
                $userRepository = $this->entityManager->getRepository(User::class);
                $currentUser = $userRepository->find($userId);
                $post = $this->postService->savePost($form->getData(), null,
                    $currentUser);
                $postLogger = $this->getLogger('post');
                $postLogger->info("Post ". $post->getId() . " has been created");
                $route = "posts";
            }
            else
            {
                $route = "posts__addition";
            }
            $this->redirectToRoute($route, $routeParams);
        }
        else
        {
            $this->twig->display('post/add.html.twig',
            [
                'formFields' => $form->getFields(),
            ]);
        }
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

    public function postByUser() : void
    {
        $userId = $this->getSession("user_id");
        $postRepository = $this->entityManager->getRepository(Post::class);
        $posts = $postRepository->findByUser($userId);

        $this->twig->display('post/byUser.html.twig',
        [
            'posts' => $posts
        ]);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function modify(array $params) : void
    {
        $postId = $params["postId"];
        $postRepository = $this->entityManager->getRepository(Post::class);
        $post = $postRepository->findById($postId)[0];

        $form = PostFormType::buildForm($post);
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $form->bind($_POST);
            $route = "";
            $routeParams = [];

            if ($form->isValid())
            {
                $postLogger = $this->getLogger('post');
                try
                {
                    $post = $this->postService->savePost($_POST, $postId, null);
                }
                catch (\Exception $exception)
                {
                    $postLogger->error($exception->getMessage());
                }
                $postLogger->info("Post ". $post->getId() . " has been updated");
                $route = "posts__details";
                $routeParams["postId"] = $postId;
            }
            else
            {
                $route = "posts__modify";
            }
            $this->redirectToRoute($route, $routeParams);
        }
        else
        {
            $this->twig->display('post/modify.html.twig',
            [
                'formFields' => $form->getFields(),
                "postId" => $postId,
            ]);
        }
    }

    public function delete (array $params) : void
    {
        $postId = $params["postId"];
        $postRepository = $this->entityManager->getRepository(Post::class);
        $post = $postRepository->findById($postId)[0];
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findByPost($postId);
        $commentLogger = $this->loggers["comment"];
        foreach($comments as $comment)
        {
            $commentLogger->warning("Comment deleted: " . $comment->getId());
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }
        $postLogger = $this->loggers["post"];
        $postLogger->warning("Deleted post with id: " . $post->getId());
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->redirectToRoute("posts");
    }
}