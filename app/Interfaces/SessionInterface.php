<?php

namespace App\Interfaces;

interface SessionInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function has(string $key);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default);

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, mixed $value);

    /**
     * @return mixed
     */
    public function clear();

    /**
     * @param string $key
     * @return mixed
     */
    public function remove(string $key);
}