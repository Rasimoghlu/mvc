<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\ValidationInterface;
use Core\Handlers\ValidationHandler;

class Validation
{
    public function __construct(private readonly ValidationInterface $validation)
    {
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(new ValidationHandler()))->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->validation, $name)) {
            return $this->validation->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}