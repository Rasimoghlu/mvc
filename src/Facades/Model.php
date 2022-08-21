<?php

namespace Src\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use Src\Handlers\QueryBuilderHandler;

class Model
{
    /**
     * @var string
     */
    protected string $table = '';

    private QueryBuilderHandler $handler;

    /**
     *
     */
    public function __construct()
    {
        $this->handler = new QueryBuilderHandler($this->getTable());
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
        if (method_exists($this->handler, $name)) {
            return $this->handler->$name(...$arguments);
        }

        throw new MethodNotFoundException();
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
       return $this->table ?? strtolower(class_basename($this).'s');
    }

    /**
     * Set the table associated with the model.
     *
     * @param string $table
     * @return $this
     */
    public function setTable(string $table): static
    {
        $this->table = $table;

        return $this;
    }
}