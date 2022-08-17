<?php

namespace App\Interfaces;

interface ViewInterface
{
    /**
     * @param string $name
     * @param array $data
     * @return void
     */
    public function send(string $name, array $data = []): void;
}