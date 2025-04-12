<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class SubmitType
{
    public static function addField(Form $form, string $label): void
    {
        $form->addField(
            'submit',
            'submit',
            'submit',
            $label,
            ''
        );
    }
}
