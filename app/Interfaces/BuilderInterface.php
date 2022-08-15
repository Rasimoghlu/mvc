<?php

namespace App\Interfaces;

interface BuilderInterface
{
    /**
     * @return object
     */
    public function get(): object;

    /**
     * @return object|bool
     */
    public function first(): object|bool;

    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param array $fields
     * @param array $tables
     * @return mixed
     */
    public function select(array $fields, array $tables = []);

    /**
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return mixed
     */
    public function where(string $field, string $operator, string|int $value);

    /**
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return mixed
     */
    public function orWhere(string $field, string $operator, string|int $value);

    /**
     * @param string $field
     * @param array $values
     * @return mixed
     */
    public function whereIn(string $field, array $values);

    /**
     * @param string $field
     * @param array $values
     * @return mixed
     */
    public function whereNotIn(string $field, array $values);

    /**
     * @param string $field
     * @param array $values
     * @return mixed
     */
    public function orWhereIn(string $field, array $values);

    /**
     * @param string $column
     * @return mixed
     */
    public function groupBy(string $column);

    /**
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return mixed
     */
    public function having(string $field, string $operator, string|int $value);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function join(string $table, string $first, string $operation, string $second);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function innerJoin(string $table, string $first, string $operation, string $second);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function leftJoin(string $table, string $first, string $operation, string $second);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function rightJoin(string $table, string $first, string $operation, string $second);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function fullJoin(string $table, string $first, string $operation, string $second);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function fullOuterJoin(string $table, string $first, string $operation, string $second);

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return mixed
     */
    public function crossJoin(string $table, string $first, string $operation, string $second);

    /**
     * @param $limit
     * @return mixed
     */
    public function limit($limit);

    /**
     * @param string $column
     * @param string $type
     * @return mixed
     */
    public function orderBy(string $column, string $type);

    /**
     * @param int $pageLimit
     * @return mixed
     */
    public function paginate(int $pageLimit);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function update(array $data);

    /**
     * @return mixed
     */
    public function delete();

}