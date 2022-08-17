<?php

namespace App\Http\Exceptions;

use Core\Facades\Request;
use Exception;

class BadMethodException extends Exception
{
    /**
     * @var int
     */
    protected $code = 405;

    public function __construct()
    {
        parent::__construct();
        $this->message = $this->message();
    }

    private function getCurrentMethod()
    {
        return Request::method();
    }

    public function message(): string
    {
       return $this->message = "Bad method allowed!. Please try to use {$this->getCurrentMethod()} method.";
    }
}