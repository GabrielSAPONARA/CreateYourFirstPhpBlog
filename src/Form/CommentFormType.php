<?php

namespace App\Form;

use App\Entity\Comment;

class CommentFormType
{
    public static function buildForm(?Comment $comment = null): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'Content',
                'textarea',
                'content',
                $comment ? $comment->getContent() : '',
                'Content',
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
                'Submit',
                '',
            )
        ;

        return $form;
    }
}