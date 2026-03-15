<?php

namespace App\Http\Exceptions;

use Exception;

class ModelNotFoundException extends Exception
{
    protected $message = 'No query results for model.';
    protected $code = 404;
}
