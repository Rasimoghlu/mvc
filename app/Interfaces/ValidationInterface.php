<?php

namespace App\Interfaces;

interface ValidationInterface
{
    public function make(array $data, array $rules);
}