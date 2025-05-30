<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class TextareaType
{
    /**
     * @param Form $form
     * @param string $label
     * @param string $name
     * @param string $value
     * @param string $placeholder
     * @param string $id
     * @param bool $isDisabled
     * @return void
     */
    public static function addField(Form   $form, string $label, string $name,
                                    string $value, string $placeholder,
                                    string $id, bool $isDisabled): void
    {
        $form->addField
        (
            $name,
            'textarea',
            $id,
            $value,
            $label,
            [
                'required'    => true,
                'placeholder' => $placeholder,
                'disabled'    => $isDisabled,
            ]
        );
    }
}