<?php

namespace App\Form;

use App\Form\Form;
use App\Entity\Role;
class RoleFormType
{
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