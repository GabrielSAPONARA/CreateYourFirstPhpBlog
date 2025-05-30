<?php

namespace App\Form;

use App\Entity\Role;
use App\Form\Form\Form;

class RoleFormType
{
    /**
     * @param Role|null $role
     * @return Form
     */
    public static function buildForm(?Role $role = null): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'name',
                'text',
                'name',
                $role ? $role->getName() : '',
                'Name',
                [
                    'required' => true,
                    'placeholder' => $role ? '' : 'Membre'
                ]
            )
            ->addField
            (
                'submit',
                'submit',
                'submit',
                'Submit',
                '',
            )
            ;
        return $form;
    }
}