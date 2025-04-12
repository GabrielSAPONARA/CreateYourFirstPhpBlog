<?php

namespace App\Form;

use App\Form\Form\Form;
use Ramsey\Uuid\UuidInterface;

class ContactFormType
{
    public static function buildForm(?UuidInterface $userId): Form
    {
        $form = new Form();

        if($userId === null)
        {
            $form
                ->addField
                (
                    'Email',
                    'email',
                    'email',
                    '',
                    'email'
                )
                ;
        }


        $form
            ->addField
            (
                'Subject',
                'text',
                'subject',
                '',
                'subject'
            )
            ->addField
            (
                'Message',
                'textarea',
                'message',
                '',
                'message'
            )
            ->addField
            (
                'submit',
                'submit',
                'submit',
                'Submit',
                '',
            )
        ;

        return $form;
    }
}