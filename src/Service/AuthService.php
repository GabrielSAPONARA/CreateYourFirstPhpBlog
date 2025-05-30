<?php

namespace App\Service;

use App\Component\Session;

class AuthService
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @param $user
     * @return void
     * @throws \Random\RandomException
     */
    public function updateSessionWithCurrentUser($user): void
    {
        $this->session->set('user_id', $user->getId());
        $this
            ->session
            ->set('username', $user->getUsername())
        ;
        $this->session->set('role', $user
            ->getRole()
            ->getName());


        $this->session->regenerateSessionId();
    }
}