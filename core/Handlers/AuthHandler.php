<?php

namespace Core\Handlers;

use App\Interfaces\AuthInterface;
use App\Models\User;
use Core\Facades\Session;

class AuthHandler implements AuthInterface
{
    private const AUTH_SESSION = 'auth';

    /**
     * @param array $data
     * @return object|string
     */
    public function login(array $data): object|string
    {
        $user = $this->checkUserExistsInDb($data);

        if ($user && get_object_vars($user)) {
            Session::set(self::AUTH_SESSION, $user);

            return $user;
        }

        return 'invalid credentionals';
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        return Session::remove(self::AUTH_SESSION);
    }

    /**
     * @param array $data
     * @return object
     */
    private function checkUserExistsInDb(array $data): object
    {
        $user = User::where('email', '=', $data['email'])->where('password', '=', sha1($data['password']))->first();

        if ($user) {
            return $user;
        }

        return new \stdClass();
    }

    /**
     * @return object|null
     */
    public function user(): object|null
    {
        return $this->getUser();
    }

    /**
     * @return null
     */
    private function getUser()
    {
        if (Session::has(self::AUTH_SESSION)) {
            $user = Session::get(self::AUTH_SESSION);

            return User::where('email', '=', $user->email)->where('password', '=', $user->password)->first();
        }

        return null;
    }

}