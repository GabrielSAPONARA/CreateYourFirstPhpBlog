<?php

namespace App\Form;

use App\Entity\Role;
use App\Form\Form;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;

class MemberFormType
{
    public static function buildForm(?User $user = null): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'Firstname',
                'text',
                'firstname',
                $user ? $user->getFirstName() : '',
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
                $user ? $user->getLastName() : '',
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
                $user ? $user->getEmailAddress() : '',
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
                $user ? $user->getUsername() : '',
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
                $user ? $user->getPassword() : '',
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