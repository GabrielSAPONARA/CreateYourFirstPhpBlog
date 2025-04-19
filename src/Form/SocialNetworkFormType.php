<?php

namespace App\Form;

use App\Entity\SocialNetwork;
use App\Form\Form\Form;

class SocialNetworkFormType
{
    public static function buildForm(?SocialNetwork $socialNetwork = null): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'name',
                'text',
                'name',
                $socialNetwork ? $socialNetwork->getName() : '',
                'Name',
                [
                    'required' => true,
                    'placeholder' => $socialNetwork ? '' : 'Facebook'
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