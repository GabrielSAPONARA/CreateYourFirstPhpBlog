<?php

namespace App\Form;

use App\Entity\Post;
use App\Form\Form;
use Doctrine\DBAL\Types\TextType;

class PostFormType
{
    public static function buildForm(?Post $post = null): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'Title',
                'text',
                'title',
                $post ? $post->getTitle() : '',
                'Title',
                [
                    'required' => true,
                    'placeholder' => 'Title of the post',
                    'maxlength' => 255,
                ]
            )
            ->addField
            (
                'Chapo',
                'text',
                'chapo',
                $post ? $post->getChapo() : '',
                'Chapo',
                [
                    'required' => true,
                    'placeholder' => 'Chapo of the post',
                    'maxlength' => 500,
                ]
            )
            ->addField
            (
                'Content',
                'textarea',
                'content',
                $post ? $post->getContent() : '',
                'Content',
                [
                    'required' => true,
                    'placeholder' => '',
                ]
            )
            ->addField
            (
                'submit',
                'submit',
                'submit',
                'Submit',
                ''
            )
            ;

        return $form;
    }
}