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

    public function __construct(Mailer $mailer, Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

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

        // Envoie l'email
        try {
            $this->mailer->send($email);
            return true;
        } catch (Exception $e) {
            // Gérer les exceptions d'envoi d'email
            echo 'Caught exception: ' . $e->getMessage() . "\n";
            return false;
        }
    }
}
