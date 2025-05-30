<?php

namespace App\Form\Type;

use App\Form\Form\Form;

class SubmitType
{
    /**
     * @param Form $form
     * @param string $label
     * @return void
     */
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
