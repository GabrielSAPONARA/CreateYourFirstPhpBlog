<?php

namespace App\Form\Type;

use App\Entity\Role;
use App\Entity\User;
use App\Form\Form\Form;

class RoleType
{
    /**
     * @param Form $form
     * @param User|null $user
     * @param array $roles
     * @return void
     */
    public static function addField(Form $form, ?User $user, array $roles): void
    {
        $roleId = $user && $user->getRole() ? $user->getRole()->getId() : null;

        $form->addField(
            'Roles',
            'radio',
            'roles',
            $roleId,
            'Roles',
            [
                'required' => true,
                'choices'  => self::mapRolesToChoices($roles),
            ]
        );
    }

    private static function mapRolesToChoices(array $roles): array
    {
        return array_map(function (Role $role)
        {
            return [
                'value' => $role->getId(),
                'label' => $role->getName(),
            ];
        }, $roles);
    }
}
