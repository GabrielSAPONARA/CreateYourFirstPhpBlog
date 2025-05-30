<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Form\Form;
use App\Form\Type\EmailType;
use App\Form\Type\PasswordType;
use App\Form\Type\RoleType;
use App\Form\Type\SubmitType;
use App\Form\Type\TextType;

class UserFormType
{
    /**
     * @param User|null $user
     * @param array $roles
     * @return Form
     */
    public static function buildForm(?User $user = null, array $roles = []): Form
    {
        $form = new Form();

        TextType::addField($form, 'Firstname', 'firstname', $user ? $user->getFirstName() : '', 'Firstname', [
            'required'    => true,
            'placeholder' => $user ? '' : 'Martin',
        ]);

        TextType::addField($form, 'Lastname', 'lastname', $user ? $user->getLastName() : '', 'Lastname', [
            'required'    => true,
            'placeholder' => $user ? '' : 'MARTIN',
        ]);

        EmailType::addField($form, 'Email Address', 'emailAddress', $user ? $user->getEmailAddress() : '', 'Email address', 'email');

        TextType::addField($form, 'Username', 'username', $user ? $user->getUsername() : '', 'Username', [
            'required'    => true,
            'placeholder' => $user ? '' : 'RikuKing',
        ]);

        PasswordType::addField($form, 'Password', 'password', $user ? $user->getPassword() : '', 'Password', [
            'required'    => true,
            'placeholder' => $user ? '' : '********************************',
        ]);

        RoleType::addField($form, $user, $roles);

        SubmitType::addField($form, 'Submit');

        return $form;
    }
}