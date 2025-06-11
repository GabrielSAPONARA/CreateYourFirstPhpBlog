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

    /**
     * @param EntityManagerInterface $entityManager
     * @param Environment $twig
     * @param RouteManager $routeManager
     * @param array $loggers
     * @param Session $session
     * @param EmailService $emailService
     */
    public function __construct
    (
        EntityManagerInterface   $entityManager,
        \Twig\Environment        $twig,
        \App\Router\RouteManager $routeManager,
        array                    $loggers,
        Session                  $session,
        EmailService             $emailService
    )
    {
        parent::__construct($twig, $routeManager, $loggers, $session);
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
    }

    /**
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function contact()
    {
        $userId = $this->getSession()->get("user_id");
        $contactLogger = $this->getLogger("contact");

        $form = ContactFormType::buildForm($userId);

        if (filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !==
            null)
        {
            $form->bind(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));

            if ($form->isValid())
            {
                $formData = $form->getData();
                $subject = $formData['Subject'];
                $message = $formData['Message'];

                $userId = $this->getSession()->get("user_id");
                $userRepository = $this->entityManager->getRepository(User::class);
                if ($userId !== null)
                {
                    $userRepository = $this->entityManager->getRepository(User::class);
                    $currentUser = $userRepository->findById($userId);
                }
                else
                {
                    // temporary user
                    $currentUser = new User();
                    $currentUser->setEmailAddress($formData['email']);
                    $currentUser->setFirstName("Disconnected user");
                    $currentUser->setLastName("Disconnected user");
                    $currentUser->setUsername("Disconnected user");

                }

                $this->emailService->sendEmail($currentUser, $subject, $message);
                $contactLogger->info("Message sent by " .
                                     $currentUser->getUsername() .
                                     "which mail address is"
                                     . $currentUser->getEmailAddress());
            }
            $this->redirectToRoute("contact");
        }

        $this->render('contact/contact.html.twig',
            [
                'formFields' => $form->getFields(),
            ]);
    }
}