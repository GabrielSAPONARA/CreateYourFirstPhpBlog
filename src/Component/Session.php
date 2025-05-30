<?php

namespace App\Component;

class Session
{
    /**
     * @var array
     */
    private $session;

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

        // $this->session = (isset($_SESSION)) ? $_SESSION : null;
        $this->session = &$_SESSION;
        $this->regenerateSessionId();
    }

    /**
     * @return array
     */
    public function getSess()
    {
        return $this->session;
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value): void
    {
        $this->session[$key] = $value;
        $_SESSION[$key] = $this->session[$key];
    }

    public function get(string $key)
    {
        return (isset($this->session[$key])?$this->session[$key]:null);
    }

    /**
     * @return void
     */
    public function destroy(): void
    {
        session_destroy();
        unset($this->session);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->session = [];
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        if (isset($this->session[$key])) {
            unset($this->session[$key]);
        }
    }

    /**
     * @param string $type
     * @param string $message
     * @param $duration
     * @return void
     */
    public function addFlashMessage(string $type, string $message, $duration = 5): void
    {
        if ($this->get('flash_messages') === null) {
            $this->set('flash_messages', []);
        }
        $this->session['flash_messages'][$type][] = [
            'message' => $message,
            'expiresAt' => time() + $duration
        ];
    }

    /**
     * @return array
     */
    public function clearFlashMessages(): array
    {
        $now = time();
        foreach ($this->get('flash_messages') as $type => $messages) {
            foreach ($messages as $index => $msg) {
                if ($msg['expiresAt'] < $now) {
                    unset($this->session['flash_messages'][$type][$index]);
                }
            }
            if (empty($this->session['flash_messages'][$type])) {
                unset($this->session['flash_messages'][$type]);
            }
        }

        $flashMessages = $this->get('flash_messages') ?? [];
        $this->remove('flash_messages');

        return $flashMessages;
    }

    /**
     * @return array
     */
    public function getFlashMessages(): array
    {
        if (!isset($this->session['flash_messages'])) {
            return [];
        }

        return $this->clearFlashMessages();
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeFlashMessage(int $index): void
    {
        if (isset($this->session['flash_messages'][$index])) {
            unset($this->session['flash_messages'][$index]);
            $this->set('flash_messages', array_values($this->session['flash_messages']));
        }
    }

    /**
     * @return void
     * @throws \Random\RandomException
     */
    public function regenerateSessionId(): void
    {
        // Save current data session
        $oldSessionData = $_SESSION;

        // Regenerate session id
        $newSessionId = bin2hex(random_bytes(32));
        session_commit();
        session_id($newSessionId);

        // Start the session with the new id
        session_start();

        // Restore the data of saved session
        $_SESSION = $oldSessionData;
        $this->session = &$_SESSION;
    }
}
