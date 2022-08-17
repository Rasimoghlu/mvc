<?php

namespace App\Interfaces;

interface ResponseInterface
{
    /**
     * @param $data
     * @return false|string
     */
    public function json($data): bool|string;
}