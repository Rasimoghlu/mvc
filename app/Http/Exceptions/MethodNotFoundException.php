<?php

namespace App\Http\Exceptions;

use Exception;

class MethodNotFoundException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Method is not found!.';

    /**
     * @var int
     */
    protected $code = 404;
}