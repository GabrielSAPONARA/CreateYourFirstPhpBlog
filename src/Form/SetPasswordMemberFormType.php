<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Form\Form;

class SetPasswordMemberFormType
{
    /**
     * @param User|null $user
     * @return Form
     */
    public static function buildForm(): Form
    {
        $form = new Form();
        $form
            ->addField
            (
                'oldPassword',
                'password',
                'oldPassword',
                '',
                'Old Password *',
                [
                    'required'    => true,
                    'placeholder' => '********************************',
                ]
            )
            ->addField
            (
                'newPassword',
                'password',
                'newPassword',
                '',
                'New Password *',
                [
                    'required'    => true,
                    'placeholder' => '********************************',
                ]
            )
            ->addField
            (
                'repeatPassword',
                'password',
                'repeatPassword',
                '',
                'Repeat Password *',
                [
                    'required'    => true,
                    'placeholder' => '********************************',
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