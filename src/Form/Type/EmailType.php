<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class EmailType
{
    public static function addField(Form $form, string $label, string $name, string $value, string $placeholder, string $id): void
    {
        $form->addField(
            $label,
            $id,
            $name,
            $value,
            'Email address',
            [
                'required' => true,
                'placeholder' => $value ? '' : $placeholder,
            ]
        );
    }
}
