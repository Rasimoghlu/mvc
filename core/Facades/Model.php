<?php

namespace Core\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Core\Handlers\QueryBuilderHandler;

class Model
{
    /**
     * @var string
     */
    protected string $table;

    /**
     * @var QueryBuilderHandler
     */
    private QueryBuilderHandler $builder;

    /**
     *
     */
    public function __construct()
    {
        $this->builder = new QueryBuilderHandler($this->table);
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
        if (method_exists($this->builder, $name)) {
            return $this->builder->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

}