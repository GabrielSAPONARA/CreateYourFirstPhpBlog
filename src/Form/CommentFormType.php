<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\Form\Form;
use App\Form\Type\SubmitType;
use App\Form\Type\TextareaType;

class CommentFormType
{
    /**
     * @param Comment|null $comment
     * @param array $options
     * @return Form
     */
    public static function buildForm(?Comment $comment = null, array $options
    = []): Form
    {
        $isDisabled = $options['disabled'] ?? false;
        $form = new Form();

        TextareaType::addField($form, 'Content', 'Content', $comment ?
            $comment->getContent() : '', 'Content of the comment', 'content',
            $isDisabled);

        SubmitType::addField($form, 'Submit');

        return $form;
    }
}