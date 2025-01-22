<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;

class PostController extends BasicController
{
    public function index() : void
    {
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $postRepository = $entityManager->getRepository(Post::class);
        $posts = $postRepository->findAll();

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

    public function process($params = []) : void
    {
        $postId = $params['id'] ?? null;
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        $url = "Location: http://";
        $host = $_SERVER["SERVER_NAME"];
        $port = $_SERVER["SERVER_PORT"];
        $url .= $host .":". $port . "/";
        if(isset($_POST["title"]))
        {
            if($postId !== null)
            {
                $postRepository = $entityManager->getRepository
                (Post::class);
                $post = $postRepository->findById($postId);
                $post->setTitle($_POST["title"]);
                $post->setChapo($_POST["chapo"]);
                $post->setContent($_POST["content"]);
                $entityManager->flush();
            }
            else
            {
                $post = new Post();
                $post->setTitle($_POST["title"]);
                $post->setChapo($_POST["chapo"]);
                $post->setContent($_POST["content"]);
                $entityManager->persist($post);
                $entityManager->flush();
            }
            $url .= "post";
        }
        else
        {
            $url .= "post/add";
        }
        header($url);
        exit();
    }
}