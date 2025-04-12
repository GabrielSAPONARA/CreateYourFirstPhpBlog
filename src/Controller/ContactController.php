<?php

namespace App\Controller;

use App\Controller\BasicController;
use App\Entity\User;
use App\Form\ContactFormType;
use App\Router\RouteManager;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class ContactController extends BasicController
{
    private EntityManagerInterface $entityManager;
    protected Environment $twig;

    private RouteManager $routeManager;

    protected array $loggers;

    private EmailService $emailService;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers,
        EmailService $emailService
    )
    {
        parent::__construct($twig, $routeManager, $loggers);
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
    }

    public function contact()
    {
        $userId = $this->getSession("user_id");
        $contactLogger = $this->getLogger("contact");
        if($userId !== null)
        {
            $userRepository = $this->entityManager->getRepository(User::class);
            $currentUser = $userRepository->findById($userId);
        }


        $form = ContactFormType::buildForm($userId);

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $form->bind($_POST);
            $route = "";
            $routeParams = [];

            if ($form->isValid())
            {
                $formData = $form->getData();
                $subject = $formData['Subject'];
                $message = $formData['Message'];

                $userId = $this->getSession("user_id");
                $userRepository = $this->entityManager->getRepository(User::class);
                $currentUser = $userRepository->findById($userId);

                $this->emailService->sendEmail($currentUser, $subject, $message);
                $contactLogger->info("Message sent by ".
                                     $currentUser->getUsername(). "which mail address is"
                                     .$currentUser->getEmail());
                $this->redirectToRoute("contact");
            }
        }

        $this->render('contact/contact.html.twig',
            [
                'formFields' => $form->getFields(),
            ]);
    }
}