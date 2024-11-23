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
                'First Name',
                'text',
                'firstName',
                $user ? $user->getFirstName() : '',
                [
                    'required' => true,
                    'placeholder' => $user ? '' : 'Martin',
                ]
            )
            ->addField
            (
                'Last Name',
                'text',
                'lastName',
                $user ? $user->getLastName() : '',
                [
                    'required' => true,
                    'placeholder' => $user ? '' : 'MARTIN',
                ]
            )
            ->addField
            (
                'Email Adress',
                'email',
                'emailAdress',
                $user ? $user->getEmailAddress() : '',
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
                'Submit'
            )
        ;

        return $form;
    }
}