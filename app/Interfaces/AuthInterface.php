<?php

namespace App\Interfaces;

interface AuthInterface
{
    /**
     * @return object|null
     */
    public function user(): object|null;

    /**
     * @param array $data
     * @return object|string
     */
    public function login(array $data): object|string;

    /**
     * @return void
     */
    public function logout();

}