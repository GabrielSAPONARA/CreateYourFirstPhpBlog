<?php

namespace App\Form;

use App\Form\Form\Form;
use App\Form\Type\EmailType;
use App\Form\Type\SubmitType;
use App\Form\Type\TextareaType;
use App\Form\Type\TextType;
use Ramsey\Uuid\UuidInterface;

class ContactFormType
{
    /**
     * @param UuidInterface|null $userId
     * @return Form
     */
    public static function buildForm(?UuidInterface $userId): Form
    {
        $form = new Form();

        if($userId === null)
        {
            EmailType::addField($form, 'email', 'Email', '', 'martin.martin@gmail.com', 'email');
        }

        TextType::addField($form, 'Subject', 'Subject', '', 'Subject');

        TextareaType::addField($form, 'message', 'Message', '', 'This is a 
        message...','message', false);

        SubmitType::addField($form, 'Submit');

        return $form;
    }
}