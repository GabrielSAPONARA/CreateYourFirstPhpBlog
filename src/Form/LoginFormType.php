<?php

namespace App\Form;

use App\Form\Form;
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
            )
            ;

        return $form;
    }
}