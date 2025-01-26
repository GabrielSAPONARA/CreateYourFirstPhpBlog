<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use JetBrains\PhpStorm\NoReturn;
use Ramsey\Uuid\Uuid;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostController extends BasicController
{
    public function index() : void
    {
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $postRepository = $entityManager->getRepository(Post::class);
        $posts = $postRepository->findAll();
//        dd($posts);
//        dd(phpinfo());

        $this->twig->display('post/index.html.twig',
            [
                'posts' => $posts
            ]);
    }

    public function add() : void
    {
        $this->beforeAction("Member");
        $form = PostFormType::buildForm();

        $this->setPreviousRoute($this->getCurrentRoute());
        $this->setCurrentRoute("posts_addition");

        $this->twig->display('post/add.html.twig',
        [
            'formFields' => $form->getFields(),
        ]);
    }

    public function process($params = []) : void
    {
        $postId = $params['id'] ?? null;
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';

        if(isset($_POST["Title"]) && strlen($_POST["Content"]) && isset
            ($_POST["Chapo"]))
        {
            $isPublished = false;
            if(!isset($_POST["IsPublished"]))
            {
                $isPublished = true;
            }

            if($postId !== null)
            {
                $postRepository = $entityManager->getRepository
                (Post::class);
                $post = $postRepository->findById($postId);
                $post->setTitle($_POST["Title"]);
                $post->setChapo($_POST["Chapo"]);
                $post->setContent($_POST["Content"]);
                $post->setIsPublished($isPublished);
                $entityManager->flush();
            }
            else
            {
                $post = new Post();
                $post->setTitle($_POST["Title"]);
                $post->setChapo($_POST["Chapo"]);
                $post->setContent($_POST["Content"]);
                $post->setIsPublished($isPublished);
                $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $currentDate->setTimezone(new \DateTimeZone('UTC'));
                $post->setDateOfLastUpdate($currentDate);
                $currentUserId = $this->getSession("user_id");
                $userRepository = $entityManager->getRepository(User::class);
                $currentUser = $userRepository->findById($currentUserId);
                $post->setUser($currentUser);
                $entityManager->persist($post);
                $entityManager->flush();
            }
            $route = "posts";
        }
        else
        {
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
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $postRepository = $entityManager->getRepository(Post::class);
        $commentRepository = $entityManager->getRepository(Comment::class);
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