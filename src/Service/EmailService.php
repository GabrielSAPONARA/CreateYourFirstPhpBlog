<?php

namespace App\Service;

use Exception;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService {
    private $mailer;
    private $twig;

    /**
     * @param Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(Mailer $mailer, Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param $user
     * @param $subject
     * @param $content
     * @return bool
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendEmail($user, $subject, $content) {
        // Crée un email avec Symfony Mailer
        $emailContent = $this->twig->render("mail/contactMail.html.twig",
        [
            'subject' => $subject,
            'content' => $content,
            'userMail' => $user->getEmailAddress(),
            "username" => $user->getUsername(),
            "firstname" => $user->getFirstName(),
            "lastname" => $user->getLastName(),
        ]);

        $email = (new Email())
            ->from("gabriel.saponara@zohomail.eu")
            ->to('gabriel.saponara@protonmail.com')
            ->subject($subject)
            ->html($emailContent);

        try {
            $this->mailer->send($email);
            return true;
        } catch (Exception $e) {
            echo 'Caught exception: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n";
            return false;
        }
    }
}
