<?php

namespace Core\Facades;

use App\Http\Controllers\Controller;
use App\Http\Exceptions\MethodNotFoundException;
use App\Interfaces\ViewInterface;
use Core\Handlers\ViewHandler;

class View
{
    /**
     *
     */
    public function __construct(private readonly ViewInterface $view)
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static(new ViewHandler()))->$name(...$arguments);
    }

    /**
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->view, $name)) {
            return $this->view->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }
}