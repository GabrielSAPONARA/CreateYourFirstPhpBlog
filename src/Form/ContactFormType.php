<?php

namespace App\Form;

class ContactFormType
{
    public static function buildForm(): Form
    {
        $form = new Form();
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