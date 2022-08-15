<?php

namespace Core\Facades;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\MethodNotFoundException;

class View
{
    /**
     * @var Controller
     */
    private Controller $controller;

    /**
     *
     */
    public function __construct()
    {
        $this->controller = new Controller();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static())->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->controller, $name)) {
            return $this->controller->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }
}