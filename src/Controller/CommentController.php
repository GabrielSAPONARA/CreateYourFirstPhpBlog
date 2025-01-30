<?php

namespace App\Controller;

use App\Controller\BasicController;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Router\RouteManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class CommentController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers
    )
    {
        parent::__construct($twig, $routeManager, $loggers);
        $this->entityManager = $entityManager;
    }
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
        if(isset($_POST["Content"]))
        {

            if($commentId !== null)
            {
                $commentRepository = $this->entityManager->getRepository
                (Comment::class);
                $comment = $commentRepository->findById($commentId);
                $comment->setContent($_POST["Content"]);
                $this->entityManager->flush();
            }
            else
            {
                $comment = new Comment();
                $comment->setContent($_POST["Content"]);
                $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $currentDate->setTimezone(new \DateTimeZone('UTC'));
                $comment->setPublishedDate($currentDate);
                $currentUserId = $this->getSession("user_id");
                $userRepository = $this->entityManager->getRepository(User::class);
                $currentUser = $userRepository->findById($currentUserId);
                $comment->setUser($currentUser);
                $postRepository = $this->entityManager->getRepository(Post::class);
                $post = $postRepository->findById($postId)[0];
                $comment->setPost($post);
                $this->entityManager->persist($comment);
                $this->entityManager->flush();
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