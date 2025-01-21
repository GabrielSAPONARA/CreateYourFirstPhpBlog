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
                'First Name',
                'text',
                'firstName',
                '',
                [
                    'required' => true,
                    'placeholder' => 'Martin',
                ]
            )
            ->addField
            (
                'Last Name',
                'text',
                'lastName',
                '',
                [
                    'required' => true,
                    'placeholder' => 'MARTIN',
                ]
            )
            ->addField
            (
                'Email Adress',
                'email',
                'emailAdress',
                '',
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
                'Submit'
            )
        ;

        return $form;
    }
}