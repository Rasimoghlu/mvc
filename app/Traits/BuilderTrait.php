<?php

namespace App\Traits;

use Src\DatabaseConnection;
use Src\Facades\Model;
use Src\Facades\Request;

trait BuilderTrait
{
    /**
     * @var DatabaseConnection
     */
    protected DatabaseConnection $db;

    /**
     * @var string
     */
    protected string $select;

    /**
     * @var array
     */
    protected array $where = [];

    /**
     * @var array
     */
    protected array $orWhere = [];

    /**
     * @var array
     */
    protected array $having = [];

    /**
     * @var array
     */
    protected array $whereIn = [];

    /**
     * @var array
     */
    protected array $orWhereIn = [];

    /**
     * @var array
     */
    protected array $whereNotIn = [];

    /**
     * @var array
     */
    protected array $join;

    /**
     * @var array
     */
    protected array $innerJoin;

    /**
     * @var array
     */
    protected array $leftJoin;

    /**
     * @var array
     */
    protected array $rightJoin;

    /**
     * @var array
     */
    protected array $fullJoin;

    /**
     * @var array
     */
    protected array $fullOuterJoin;

    /**
     * @var array
     */
    protected array $crossJoin;

    /**
     * @var string
     */
    protected string $limit;

    /**
     * @var array
     */
    protected array $orderBy;

    /**
     * @var array
     */
    protected array $groupBy;

    /**
     * @var string
     */
    protected string $table;

    /**
     * @var int
     */
    protected int $startPage = 1;

    /**
     * @var array
     */
    protected array $params = [];

    public function __construct($table)
    {
        $this->table = $table;
        $this->db = DatabaseConnection::getInstance();
    }

    /**
     * @param array $fields
     * @param array $tables
     * @return $this
     */
    public function select(array $fields, array $tables = []): static
    {
        if (count($tables)) {
            $tables = [$this->table, implode(', ', $tables)];
        }

        if (empty($tables)) {
            $tables = explode(', ', $this->table);
        }

        $this->select = "SELECT " . implode(', ', $fields) . ' FROM ' . implode(', ', $tables);

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return $this
     */
    public function where(string $field, string $operator, string|int $value): static
    {
        $this->params[] = $value;

        $this->where[] = "$field $operator ?";

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return $this
     */
    public function orWhere(string $field, string $operator, string|int $value): static
    {
        $this->params[] = $value;

        $this->orWhere[] = "$field $operator ?";

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string|int $value
     * @return $this
     */
    public function having(string $field, string $operator, string|int $value): static
    {
        $this->params[] = $value;

        $this->having[] = "$field $operator ?";

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @return $this
     */
    public function whereIn(string $field, array $values): static
    {
        $this->params[] = $values;

        $implodeArray = $this->implodeArrayDataAndReplaceWithQuestionMark($values);

        $this->whereIn[] = "$field IN ($implodeArray)";

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @return $this
     */
    public function orWhereIn(string $field, array $values): static
    {
        $this->params[] = $values;

        $implodeArray = $this->implodeArrayDataAndReplaceWithQuestionMark($values);

        $this->orWhereIn[] = "$field IN ($implodeArray)";

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @return $this
     */
    public function whereNotIn(string $field, array $values): static
    {
        $this->params[] = $values;

        $implodeArray = $this->implodeArrayDataAndReplaceWithQuestionMark($values);

        $this->whereNotIn[] = "$field NOT IN ($implodeArray)";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function join(string $table, string $first, string $operation, string $second): static
    {
        $this->join[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function innerJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->innerJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function leftJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->leftJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function rightJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->rightJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function fullJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->fullJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function crossJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->crossJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $operation
     * @param string $second
     * @return $this
     */
    public function fullOuterJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->fullOuterJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit): static
    {
        $this->limit = " LIMIT " . $limit;

        return $this;
    }

    /**
     * @param string $column
     * @param string $type
     * @return $this
     */
    public function orderBy(string $column, string $type): static
    {
        $this->orderBy[] = $column . ' ' . $type;

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function groupBy(string $column): static
    {
        $this->groupBy[] = $column;

        return $this;
    }

    /**
     * @return object|bool
     */
    public function first(): object|bool
    {
        $sql = $this->prepareQuery();

        $statement = $this->db->connect()->prepare("$sql");
        $statement->execute(array_values(arrayFlatten($this->params)));

        return $statement->fetch();
    }

    /**
     * @param int $id
     * @return mixed
     * @return mixed
     */
    public function findById(int $id): mixed
    {
        $table = $this->table;

        $sql = "SELECT * FROM $table WHERE id = ?";

        $statement = $this->db->connect()->prepare("$sql");

        $statement->execute([$id]);

        return $statement->fetch();
    }

    /**
     * @return object
     */
    public function get(): object
    {
        $sql = $this->prepareQuery();

        $statement = $this->db->connect()->prepare("$sql");

        $statement->execute(array_values(arrayFlatten($this->params)));

        return (object)$statement->fetchAll();
    }

    /**
     * @return string
     */
    private function prepareQuery(): string
    {
        $sql = !isset($this->select) ? $this->getAllDataWithOutConditions() : $this->select;

        $sql .= $this->joins();

        $sql .= $this->whereQuery();

        $sql .= $this->orWhereQuery();

        $sql .= $this->whereInQuery();

        $sql .= $this->orWhereInQuery();

        $sql .= $this->whereNotInQuery();

        $sql .= $this->groupByQuery();

        $sql .= $this->havingQuery();

        $sql .= $this->orderByQuery();

        $sql .= $this->limitQuery();

        return $sql;
    }

    /**
     * @return string|null
     */
    private function limitQuery(): ?string
    {
        if (isset($this->limit)) {
            return $this->limit;
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function groupByQuery(): ?string
    {
        if (isset($this->groupBy)) {
            return " GROUP BY " . implode(', ', $this->groupBy);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function orderByQuery(): ?string
    {
        if (isset($this->orderBy)) {
            return " ORDER BY " . implode(', ', $this->orderBy);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function whereInQuery(): ?string
    {
        if (!empty($this->whereIn)) {
            return " WHERE " . implode(' AND ', $this->whereIn);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function orWhereInQuery(): ?string
    {
        if (empty($this->whereIn) && !empty($this->orWhereIn)) {
            return " WHERE " . implode(' OR ', $this->orWhereIn);
        } elseif (!empty($this->orWhereIn)) {
            return " OR " . implode(' OR ', $this->orWhereIn);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function whereNotInQuery(): ?string
    {
        if (!empty($this->whereNotIn)) {
            return " WHERE " . implode(' AND ', $this->whereNotIn);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function whereQuery(): ?string
    {
        if (!empty($this->where)) {
            return " WHERE " . implode(' AND ', $this->where);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function orWhereQuery(): ?string
    {
        if (empty($this->where) && !empty($this->orWhere)) {
            return " WHERE " . implode(' OR ', $this->orWhere);
        } elseif (!empty($this->orWhere)) {
            return " OR " . implode(' OR ', $this->orWhere);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function havingQuery(): ?string
    {
        if (!empty($this->having)) {
            return " HAVING " . implode(' AND ', $this->having);
        }

        return null;
    }

    /**
     * @return string
     */
    private function joins(): string
    {
        $sql = '';

        if (!empty($this->join)) {
            $sql .= " JOIN " . implode(' JOIN ', $this->join);
        }

        if (!empty($this->innerJoin)) {
            $sql .= " INNER JOIN " . implode(' INNER JOIN ', $this->innerJoin);
        }

        if (!empty($this->leftJoin)) {
            $sql .= " LEFT JOIN " . implode(' LEFT JOIN ', $this->leftJoin);
        }

        if (!empty($this->rightJoin)) {
            $sql .= " RIGHT JOIN " . implode(' RIGHT JOIN ', $this->rightJoin);
        }

        if (!empty($this->fullJoin)) {
            $sql .= " FULL JOIN " . implode(' FULL JOIN ', $this->fullJoin);
        }

        if (!empty($this->fullOuterJoin)) {
            $sql .= " FULL OUTER JOIN " . implode(' FULL OUTER JOIN ', $this->fullOuterJoin);
        }

        if (!empty($this->crossJoin)) {
            $sql .= " CROSS JOIN " . implode(' CROSS JOIN ', $this->crossJoin);
        }

        return $sql;
    }

    /**
     * @return string
     */
    public function getAllDataWithOutConditions(): string
    {
        return "SELECT * FROM " . $this->table;
    }

    /**
     * @param int $pageLimit
     * @return object
     */
    public function paginate(int $pageLimit = 10): object
    {
        $sql = $this->prepareQuery();

        $page = Request::get('page');

        $page = !isset($page) ? $this->startPage : $page;

        if ($page > 0) {
            $startLimit = ($page * $pageLimit) - $pageLimit;
        } else {
            $startLimit = ($this->startPage * $pageLimit) - $pageLimit;
        }

        $totalRecord = $this->count();

        $totalPage = ceil($totalRecord / $pageLimit);

        $sql .= " LIMIT ?, ?";

        $this->params[] = [$startLimit];
        $this->params[] = [$pageLimit];

        $paginate = $this->db->connect()->prepare("$sql");

        $paginate->execute(array_values(arrayFlatten($this->params)));

        $result = $paginate->fetchAll();

        $result['count'] = $totalRecord;

        $result['totalPage'] = $totalPage;

        $result['perPage'] = $pageLimit;

        $result['page'] = $page;

        return (object)$result;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $sql = $this->prepareQuery();

        $statement = $this->db->connect()->prepare("$sql");
        $statement->execute(array_values(arrayFlatten($this->params)));

        return $statement->rowCount();
    }

}