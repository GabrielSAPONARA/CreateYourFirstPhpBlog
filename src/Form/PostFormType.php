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
                '',
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
                '',
                'Content',
                [
                    'required' => true,
                    'placeholder' => '',
                ]
            )
            ->addField
            (
                'IsPublished',
                'checkbox',
                'isPublished',
                'isPublished',
                'Publish ?',
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