<?php

namespace App\Form;

use App\Form\Form\Form;

class LoginFormType
{
    public static function buildForm() : Form
    {
        $form = new Form();

        $form
            ->addField
            (
                "username",
                "text",
                "username",
                "",
                'Username',
                [
                    "required" => true,
                    "placeholder" => "Username",
                ]
            )
            ->addField
            (
                "password",
                "password",
                "password",
                "",
                'Password',
                [
                    "required" => true,
                    "placeholder" => "***********",
                ]
            )
            ->addField
            (
                "Sign in",
                "submit",
                "signIn",
                "Sign in",
                '',
            )
            ;

        return $form;
    }
}