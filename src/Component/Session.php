<?php

namespace App\Component;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '', // Change if you have a domain name
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax' // Put 'None' if you need and if you use HTTPS
            ]);
            session_start();
        }
        session_regenerate_id(true);
    }

    public function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public function destroy(): void
    {
        session_destroy();
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function remove(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function addFlashMessage(string $type, string $message, $duration = 5): void
    {
        if ($this->get('flash_messages') === null) {
            $this->set('flash_messages', []);
        }
        $_SESSION['flash_messages'][$type][] = [
            'message' => $message,
            'expiresAt' => time() + $duration
        ];
    }

    public function clearFlashMessages(): array
    {
        $now = time();
        foreach ($this->get('flash_messages') as $type => $messages) {
            foreach ($messages as $index => $msg) {
                if ($msg['expiresAt'] < $now) {
                    unset($this->get('flash_messages')[$type][$index]);
                }
            }
            if (empty($this->get('flash_messages')[$type])) {
                unset($this->get('flash_messages')[$type]);
            }
        }

        $flashMessages = $this->get('flash_messages') ?? [];
        $this->remove('flash_messages');

        return $flashMessages;
    }

    public function getFlashMessages(): array
    {
        if (!isset($_SESSION['flash_messages'])) {
            return [];
        }

        return $this->clearFlashMessages();
    }

    public function removeFlashMessage(int $index): void
    {
        if (isset($this->get('flash_messages')[$index])) {
            unset($this->get('flash_messages')[$index]);
            $this->set('flash_messages', array_values($this->get('flash_messages')));
        }
    }
}
