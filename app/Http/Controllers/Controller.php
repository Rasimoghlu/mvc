<?php

namespace App\Http\Controllers;

class Controller
{
    /**
     * @param string $name
     * @param array $data
     * @return void
     */
    public function send(string $name, array $data = [])
    {
        extract($data);

        require __DIR__ . '/../../../view/' . strtolower($name) . '.php';
    }

}