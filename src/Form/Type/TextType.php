<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class TextType
{
    /**
     * @param Form $form
     * @param string $label
     * @param string $name
     * @param string $value
     * @param string $placeholder
     * @return void
     */
    public static function addField(Form $form, string $label, string $name, string $value, string $placeholder): void
    {
        $form->addField(
            $label,
            'text',
            $name,
            $value,
            $label,
            [
                'required'    => true,
                'placeholder' => $value ? '' : $placeholder,
            ]
        );
    }
}
