<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class PasswordType
{
    public static function addField(Form $form, string $label, string $name, string $value): void
    {
        $form->addField(
            $label,
            'password',
            $name,
            $value,
            'Password',
            [
                'required' => true,
                'placeholder' => $value ? '' : '********************************',
            ]
        );
    }
}
