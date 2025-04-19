<?php

namespace App\Controller;

use App\Component\Session;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Router\RouteManager;
use App\Service\CommentService;
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

    private Session $session;

    private CommentService $commentService;


    public function __construct
    (
        PostService $postService,
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers,
        Session $session,
        CommentService $commentService
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
        $this->entityManager = $entityManager;
        $this->postService = $postService;
        $this->commentService = $commentService;
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
        $this->beforeAction("Administrator");
        $form = PostFormType::buildForm();

        if(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !== null)
        {
            $form->bind(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));
            $route = "";
            $routeParams = [];


            if ($form->isValid())
            {
                $userId = $this->getSession()->get("user_id");
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
        $comments = array_filter($comments, function (Comment $comment)
        {
            return $comment->isValidated();
        });
        usort($comments, function ($commentA, $commentB) {
            return $commentB->getPublishedDate() <=> $commentA->getPublishedDate();
        });
        $post = $postRepository->findById($uuid)[0];
        $author = $post->getUser();

        $flashMessages = $this->getSession()->getFlashMessages();
        $this->render('post/detail.html.twig',
        [
            'post' => $post,
            'author' => $author,
            'comments' => $comments,
            'flashMessages' => $flashMessages,
        ]);
    }

    public function postByUser() : void
    {
        $userId = $this->getSession()->get("user_id");
        $postRepository = $this->entityManager->getRepository(Post::class);
        $posts = $postRepository->findByUser($userId);

        $this->twig->display('post/byUser.html.twig',
        [
            'posts' => $posts
        ]);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    public function modify(array $params) : void
    {
        $this->beforeAction("Administrator");
        $postId = $params["postId"];

        $post = $this->postService->findByPostId($postId);

        $form = PostFormType::buildForm($post);
        if(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !== null)
        {
            $form->bind(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));
            $route = "";
            $routeParams = [];

            if ($form->isValid())
            {
                $postLogger = $this->getLogger('post');
                try
                {
                    $post = $this->postService->savePost(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS), $postId, null);
                }
                catch (\Exception $exception)
                {
                    $postLogger->error($exception->getMessage());
                    $this->getSession()->addFlashMessage('error', $exception->getMessage
                    ());
                }
                $postLogger->info("Post ". $post->getId() . " has been updated");
                $this->getSession()->addFlashMessage('success', 'Post has been updated');
                $route = "posts__details";
                $routeParams["postId"] = $postId;
            }
            else
            {
                $route = "posts__modify";
                $this->getSession()->addFlashMessage('error', 'You have an error in your field');
            }
            $this->redirectToRoute($route, $routeParams);
        }
        else
        {
            $flashMessages = $this->getSession()->getFlashMessages();
            $this->render('post/modify.html.twig',
            [
                'formFields' => $form->getFields(),
                "postId" => $postId,
                "flashMessages" => $flashMessages,
            ]);
        }
    }

    public function delete (array $params) : void
    {
        $this->beforeAction("Administrator");
        $postId = $params["postId"];
        $post = $this->postService->findByPostId($postId);
        $comments = $this->commentService->findByPostId($postId);
        $commentLogger = $this->loggers["comment"];
        foreach($comments as $comment)
        {
            $commentLogger->warning("Comment deleted: " . $comment->getId());
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
        }
        $postLogger = $this->loggers["post"];
        $postLogger->warning("Deleted post with id: " . $post->getId());
        $this->getSession()->addFlashMessage('success', 'Post has been deleted');
        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->redirectToRoute("posts");
    }

}