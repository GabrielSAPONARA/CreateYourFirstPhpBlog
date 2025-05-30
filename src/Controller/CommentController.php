<?php

namespace App\Controller;

use App\Component\Session;
use App\Controller\BasicController;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Router\RouteManager;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CommentController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;
    private RouteManager $routeManager;
    protected array $loggers;

    private Session $session;

    private CommentService $commentService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Environment $twig
     * @param RouteManager $routeManager
     * @param array $loggers
     * @param Session $session
     * @param CommentService $commentService
     */
    public function __construct
    (
        EntityManagerInterface   $entityManager,
        \Twig\Environment        $twig,
        \App\Router\RouteManager $routeManager,
        array                    $loggers,
        Session                  $session,
        CommentService           $commentService
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
        $this->entityManager = $entityManager;
        $this->commentService = $commentService;
    }

    /**
     * @param $params
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function add($params)
    {
        $postId = $params['postId'];
        $this->beforeAction("Member");
        $form = CommentFormType::buildForm();

        $this->twig->display("comment/add.html.twig",
            [
                'formFields' => $form->getFields(),
                'postId'     => $postId
            ]);
    }

    /**
     * Undocumented function
     * @param [type] $params
     * @return void
     */
    public function process($params): void
    {
        $commentLogger = $this->getLogger("comment");
        $commentId = $params['id'] ?? null;
        $postId = $params['postId'];

        if ((filter_input(INPUT_POST, "Content", FILTER_SANITIZE_SPECIAL_CHARS) !==
             null))
        {

            if ($commentId !== null)
            {
                $commentLogger->notice("Comment " . $commentId .
                                       "was updated.");
                $commentRepository = $this->entityManager->getRepository
                (Comment::class);
                $comment = $commentRepository->findById($commentId);
                $comment->setContent(filter_input(INPUT_POST, "Content", FILTER_SANITIZE_SPECIAL_CHARS));
                $this->entityManager->flush();
            }
            else
            {
                $comment = new Comment();
                $comment->setContent(filter_input(INPUT_POST, "Content", FILTER_SANITIZE_SPECIAL_CHARS));
                $currentDate = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $currentDate->setTimezone(new \DateTimeZone('UTC'));
                $comment->setPublishedDate($currentDate);
                $currentUserId = $this->getSession()->get("user_id");
                $userRepository = $this->entityManager->getRepository(User::class);
                $currentUser = $userRepository->findById($currentUserId);
                $comment->setUser($currentUser);
                $postRepository = $this->entityManager->getRepository(Post::class);
                $post = $postRepository->findById($postId)[0];
                $comment->setPost($post);
                $comment->setIsValidated(false);
                $commentLogger->notice("New comment was created.");
                $this->entityManager->persist($comment);
                $this->entityManager->flush();
            }
            $route = "posts__details";
        }
        else
        {
            $route = "posts__details";
        }
        $this->redirectToRoute($route, ["postId" => $postId]);
    }

    public function commentByUser(): void
    {
        $userId = $this->getSession()->get("user_id");
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findBy(["user" => $userId]);

        $postIds = [];
        foreach ($comments as $comment)
        {
            $postIds[] = $comment->getPost()->getId();
        }

        $this->twig->display("comment/byUser.html.twig",
            [
                "comments" => $comments,
                "postIds"  => $postIds
            ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function modify(array $params)
    {
        $this->beforeAction("Administrator");
        $commentId = $params['commentId'];
        $postId = $params['postId'];
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comment = $commentRepository->findById($commentId)[0];

        $form = CommentFormType::buildForm($comment);
        if (filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !==
            null)
        {
            $form->bind(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));
            $route = "";
            $routeParams = [];

            if ($form->isValid())
            {
                $this->commentService->saveComment($form->getData(), $commentId, null);
                $route = "posts__details";
                $routeParams["postId"] = $postId;
            }
            else
            {
                $route = "comments__modify";
                $routeParams["commentId"] = $commentId;
            }
            $this->redirectToRoute($route, $routeParams);
        }
        else
        {
            $this->twig->display("comment/modify.html.twig",
                [
                    "formFields" => $form->getFields(),
                    "commentId"  => $commentId,
                    "postId"     => $postId
                ]);
        }
    }

    /**
     * @throws \Exception
     */
    public function delete(array $params): void
    {
        $this->beforeAction("Administrator");
        $commentId = $params['commentId'];
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $commentLogger = $this->getLogger("comment");
        if ($commentId === null)
        {
            $commentLogger->error("Comment id $commentId not found");
            throw new \Exception("Comment id $commentId not found");
        }
        $comment = $commentRepository->findById($commentId)[0];
        $postId = $comment->getPost()->getId();


        $commentLogger->warning("Comment with id $commentId deleted");
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        $this->redirectToRoute("posts__details", ["postId" => $postId]);
    }

    public function getCommentsToValidate()
    {
        $this->beforeAction("Moderator");
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findByIsValidated(false);

        $postIds = [];
        foreach ($comments as $comment)
        {
            $postIds[] = $comment->getPost()->getId();
        }

        usort($comments, function ($commentA, $commentB)
        {
            return $commentB->getPublishedDate() <=>
                   $commentA->getPublishedDate();
        });

        $this->render("comment/toValidate.html.twig",
            [
                "comments" => $comments,
                "postIds"  => $postIds
            ]);
    }

    public function validateComment(array $params)
    {
        $this->beforeAction("Moderator");
        $commentId = $params['commentId'];
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comment = $commentRepository->findById($commentId)[0];
        $options = [];
        $options["disabled"] = true;

        $form = CommentFormType::buildForm($comment, $options);

        $form
            ->addField
            (
                "IsValidated",
                'checkbox',
                "isValidated",
                "isValidated",
                "Is validate ?"
            )
        ;

        if (filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !==
            null)
        {
            $form->bind(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));
            $data = $form->getData();
            if ($data["IsValidated"] !== null)
            {
                $comment->setIsValidated(true);
                $this->entityManager->flush();
            }
            $this->redirectToRoute("comments__toValidate");
        }

        $this->render('comment/validateComment.html.twig',
            [
                "formFields" => $form->getFields(),
                "commentId"  => $commentId,
            ]);
    }
}