<?php

namespace App\Interfaces;

interface RequestInterface
{
    /**
     * @return mixed
     */
    public function method();

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @return mixed
     */
    public function post(string $key);

    /**
     * @return mixed
     */
    public function all();

}