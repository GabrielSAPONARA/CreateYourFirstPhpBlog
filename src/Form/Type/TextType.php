<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class TextType
{
    public static function addField(Form $form, string $label, string $name, string $value, string $placeholder): void
    {
        $form->addField(
            $label,
            'text',
            $name,
            $value,
            $label,
            [
                'required' => true,
                'placeholder' => $value ? '' : $placeholder,
            ]
        );
    }
}
