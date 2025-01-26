<?php

namespace App\Form;

use App\Entity\Role;
use App\Form\Form;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;

class MemberFormType
{
    public static function buildForm(): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'Firstname',
                'text',
                'firstname',
                '',
                'Firstname',
                [
                    'required' => true,
                    'placeholder' => 'Martin',
                ]
            )
            ->addField
            (
                'Lastname',
                'text',
                'lastname',
                '',
                'Lastname',
                [
                    'required' => true,
                    'placeholder' => 'MARTIN',
                ]
            )
            ->addField
            (
                'Email Address',
                'email',
                'emailAddress',
                '',
                'Email Address',
                [
                    'required' => true,
                    'placeholder' => 'martin.martin@gmail.com',
                ]
            )
            ->addField
            (
                'Username',
                'text',
                'username',
                '',
                'Username',
                [
                    'required' => true,
                    'placeholder' => 'RikuKing',
                ]
            )
            ->addField
            (
                'Password',
                'password',
                'password',
                '',
                'Password',
                [
                    'required' => true,
                    'placeholder' => '********************************',
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