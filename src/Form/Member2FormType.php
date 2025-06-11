<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Form\Form;

class Member2FormType
{
    /**
     * @param User|null $user
     * @return Form
     */
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
                    'required'    => true,
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
                    'required'    => true,
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
                    'required'    => true,
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
                    'required'    => true,
                    'placeholder' => 'RikuKing',
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