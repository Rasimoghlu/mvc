<?php

namespace Src\Handlers;

use App\Interfaces\SessionInterface;

class SessionHandler implements SessionInterface
{
    private bool $isStarted = false;

    public function isStarted(): bool
    {
        $this->isStarted = session_status() === PHP_SESSION_ACTIVE;

        return $this->isStarted;
    }

    public function start(): bool
    {
        if ($this->isStarted) {
            return true;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->isStarted = true;

            return true;
        }

        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.use_strict_mode', '1');

        session_start();
        $this->isStarted = true;

        return true;
    }

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

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function token(): string
    {
        if ($this->has('_token')) {
            return $this->get('_token');
        }

        $token = bin2hex(random_bytes(32));
        $this->set('_token', $token);

        return $token;
    }
}
