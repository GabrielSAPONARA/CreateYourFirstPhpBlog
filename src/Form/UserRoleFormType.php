<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Form\Form;
use App\Form\Type\RoleType;
use App\Form\Type\SubmitType;

class UserRoleFormType
{
    /**
     * @param User|null $user
     * @param array $roles
     * @return Form
     */
    public static function buildForm(?User $user = null, array $roles = []): Form
    {
        $form = new Form();

        RoleType::addField($form, $user, $roles);

        SubmitType::addField($form, 'Submit');

        return $form;
    }
}