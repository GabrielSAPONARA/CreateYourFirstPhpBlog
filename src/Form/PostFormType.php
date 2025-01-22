<?php

namespace App\Form;

use App\Entity\Post;
use App\Form\Form;
use Doctrine\DBAL\Types\TextType;

class PostFormType
{
    public static function buildForm(): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'Title',
                'text',
                'title',
                '',
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
                '',
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
                '',
                [
                    'required' => true,
                    'placeholder' => 'Content of the post',
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