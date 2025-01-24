<?php

namespace App\Controller;

use App\Controller\BasicController;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentFormType;

class CommentController extends BasicController
{
    public function add($params)
    {
        $postId = $params['postId'];
        $this->beforeAction("Member");
        $form = CommentFormType::buildForm();
//        dd($form);
        $this->twig->display("comment/add.html.twig",
        [
            'formFields' => $form->getFields(),
            'postId' => $postId
        ]);
    }

    public function process($params) :void
    {
        $commentId = $params['id'] ?? null;
        $postId = $params['postId'];
        $entityManager = require_once __DIR__ . '/../../bootstrap.php';
        if(isset($_POST["Content"]))
        {

            if($commentId !== null)
            {
                $commentRepository = $entityManager->getRepository
                (Comment::class);
                $comment = $commentRepository->findById($commentId);
                $comment->setContent($_POST["Content"]);
                $entityManager->flush();
            }
            else
            {
                $comment = new Comment();
                $comment->setContent($_POST["Content"]);
                $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $currentDate->setTimezone(new \DateTimeZone('UTC'));
                $comment->setPublishedDate($currentDate);
                $currentUserId = $this->getSession("user_id");
                $userRepository = $entityManager->getRepository(User::class);
                $currentUser = $userRepository->findById($currentUserId);
                $comment->setUser($currentUser);
                $postRepository = $entityManager->getRepository(Post::class);
                $post = $postRepository->findById($postId)[0];
                $comment->setPost($post);
                $entityManager->persist($comment);
                $entityManager->flush();
            }
            $route = "posts_details";
        }
        else
        {
            $route = "posts_details";
        }
        $this->redirectToRoute($route, ["postId" => $postId]);
    }
}