<?php

namespace App\Form;

use App\Entity\Role;
use App\Form\Form;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;

class UserFormType
{
    public static function buildForm(?User $user = null, array $roles = []): Form
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
                    'placeholder' => $user ? '' : 'Martin',
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
                    'placeholder' => $user ? '' : 'MARTIN',
                ]
            )
            ->addField
            (
                'Email Address',
                'email',
                'emailAddress',
                $user ? $user->getEmailAddress() : '',
                'Email address',
                [
                    'required' => true,
                    'placeholder' => $user ? '' : 'martin.martin@gmail.com',
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
                    'placeholder' => $user ? '' : 'RikuKing',
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
                    'placeholder' => $user ? '' : '********************************',
                ]
            )
            ;

        $roleId = $user && $user->getRole() ? $user->getRole()->getId() : null;

        $form
            ->addField
            (
                'Roles',
                'radio',
                'roles',
                $roleId,
                'Roles',
                [
                    'required' => true,
                    'choices' => array_map(function (Role $role)
                    {
                        return
                        [
                            'value' => $role->getId(),
                            'label' => $role->getName(),
                        ];
                    }
                    , $roles)
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