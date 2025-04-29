<?php

namespace Src\Handlers;

use App\Interfaces\SessionInterface;

class SessionHandler implements SessionInterface
{
    /**
     * @var bool
     */
    private bool $isStarted = false;

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        $this->isStarted = session_status() === PHP_SESSION_ACTIVE;

        return $this->isStarted;
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        if ($this->isStarted) {
            return true;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->isStarted = true;

            return true;
        }

        session_start();
        $this->isStarted = true;

        return true;
    }

    /**
     * @return object
     */
    public function getAuthSessionKey(): object
    {
        if ($this->has('auth')) {
            return $_SESSION['auth'];
        }

        return new \stdClass();
    }

    public function getSessionUser(): object
    {
        return $this->getAuthSessionKey();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
            session_destroy();
        }
    }

    /**
     * Create or return existing CSRF token
     * 
     * @return string
     */
    public function token(): string
    {
        // If a token already exists, return it
        if ($this->has('_token')) {
            return $this->get('_token');
        }
        
        // If no token exists, create a new one
        $token = bin2hex(random_bytes(32)); // Create a more secure token
        $this->set('_token', $token);
        
        return $token;
    }

}