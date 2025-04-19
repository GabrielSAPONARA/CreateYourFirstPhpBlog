<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class TextareaType
{
    public static function addField(Form $form, string $label, string $name,
                                    string $value, string $placeholder,
                                    string $id, bool $isDisabled) : void
    {
        $form->addField
        (
            $name,
            'textarea',
            $id,
            $value,
            $label,
            [
                'required' => true,
                'placeholder' => $placeholder,
                'disabled' => $isDisabled,
            ]
        );
    }
}