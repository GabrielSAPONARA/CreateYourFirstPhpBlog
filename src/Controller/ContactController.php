<?php

namespace App\Controller;

use App\Component\Session;
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

    private Session $session;

    private EmailService $emailService;

    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Twig\Environment $twig,
        \App\Router\RouteManager $routeManager,
        array $loggers,
        Session $session,
        EmailService $emailService
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
    }

    public function contact()
    {
        $userId = $this->getSession()->get("user_id");
        $contactLogger = $this->getLogger("contact");
        if($userId !== null)
        {
            $userRepository = $this->entityManager->getRepository(User::class);
            $currentUser = $userRepository->findById($userId);
        }


        $form = ContactFormType::buildForm($userId);

        if(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS) === 'POST')
        {
            $form->bind(filter_input_array(INPUT_POST));

            if ($form->isValid())
            {
                $formData = $form->getData();
                $subject = $formData['Subject'];
                $message = $formData['Message'];

                $userId = $this->getSession()->get("user_id");
                $userRepository = $this->entityManager->getRepository(User::class);
                $currentUser = $userRepository->findById($userId);

                $this->emailService->sendEmail($currentUser, $subject, $message);
                $contactLogger->info("Message sent by ".
                                     $currentUser->getUsername(). "which mail address is"
                                     .$currentUser->getEmailAddress());
            }
            $this->redirectToRoute("contact");
        }

        $this->render('contact/contact.html.twig',
            [
                'formFields' => $form->getFields(),
            ]);
    }
}