<?php

namespace App\Form;

class CommentFormType
{
    public static function buildForm(): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'Content',
                'textarea',
                'content',
                '',
                [
                    'required' => true,
                    'placeholder' => 'Content of the comment',
                ]
            )
            ->addField
            (
                'submit',
                'submit',
                'submit',
                'Submit'
            )
        ;

        return $form;
    }
}