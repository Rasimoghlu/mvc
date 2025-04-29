<?php

namespace App\Traits;

use DateTimeInterface;
use Exception;
use Src\DatabaseConnection;
use Src\Facades\Model;
use Src\Facades\Request;

/**
 * Builder Trait for query building
 * 
 * Provides methods for building SQL queries in a fluent interface
 */
trait BuilderTrait
{
    /**
     * Database connection instance
     * 
     * @var DatabaseConnection
     */
    protected DatabaseConnection $db;

    /**
     * SQL SELECT statement
     * 
     * @var string
     */
    protected string $select;

    /**
     * SQL WHERE clauses with AND operator
     * 
     * @var array
     */
    protected array $where = [];

    /**
     * SQL WHERE clauses with OR operator
     * 
     * @var array
     */
    protected array $orWhere = [];

    /**
     * SQL HAVING clauses
     * 
     * @var array
     */
    protected array $having = [];

    /**
     * SQL WHERE IN clauses
     * 
     * @var array
     */
    protected array $whereIn = [];

    /**
     * SQL WHERE IN clauses with OR operator
     * 
     * @var array
     */
    protected array $orWhereIn = [];

    /**
     * SQL WHERE NOT IN clauses
     * 
     * @var array
     */
    protected array $whereNotIn = [];

    /**
     * SQL JOIN clauses
     * 
     * @var array
     */
    protected array $join = [];

    /**
     * SQL INNER JOIN clauses
     * 
     * @var array
     */
    protected array $innerJoin = [];

    /**
     * SQL LEFT JOIN clauses
     * 
     * @var array
     */
    protected array $leftJoin = [];

    /**
     * SQL RIGHT JOIN clauses
     * 
     * @var array
     */
    protected array $rightJoin = [];

    /**
     * SQL FULL JOIN clauses
     * 
     * @var array
     */
    protected array $fullJoin = [];

    /**
     * SQL FULL OUTER JOIN clauses
     * 
     * @var array
     */
    protected array $fullOuterJoin = [];

    /**
     * SQL CROSS JOIN clauses
     * 
     * @var array
     */
    protected array $crossJoin = [];

    /**
     * SQL LIMIT clause
     * 
     * @var string
     */
    protected string $limit;

    /**
     * SQL OFFSET clause
     * 
     * @var string
     */
    protected string $offset;

    /**
     * SQL ORDER BY clauses
     * 
     * @var array
     */
    protected array $orderBy = [];

    /**
     * SQL GROUP BY clauses
     * 
     * @var array
     */
    protected array $groupBy = [];

    /**
     * Table name
     * 
     * @var string
     */
    protected string $table;

    /**
     * Starting page for pagination
     * 
     * @var int
     */
    protected int $startPage = 1;

    /**
     * Query parameters for prepared statements
     * 
     * @var array
     */
    protected array $params = [];

    /**
     * Raw SQL fragments to be inserted into the query
     * 
     * @var array
     */
    protected array $rawExpressions = [];

    /**
     * Constructor
     *
     * @param string $table Table name
     */
    public function __construct($table)
    {
        $this->table = $table;
        $this->db = DatabaseConnection::getInstance();
    }

    /**
     * Add a raw SQL expression to the query
     *
     * @param string $expression Raw SQL expression
     * @param array $bindings Parameters to be bound to the expression
     * @return $this
     */
    public function raw(string $expression, array $bindings = []): static
    {
        $this->rawExpressions[] = $expression;
        
        if (!empty($bindings)) {
            foreach ($bindings as $binding) {
                $this->params[] = $binding;
            }
        }
        
        return $this;
    }

    /**
     * Add SELECT clause to the query
     *
     * @param array $fields Fields to select
     * @param array $tables Additional tables
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
     * Add a WHERE clause with AND operator
     *
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param string|int $value Value to compare
     * @return $this
     */
    public function where(string $field, string $operator, string|int $value): static
    {
        $this->params[] = $value;

        $this->where[] = "$field $operator ?";

        return $this;
    }

    /**
     * Add a WHERE clause with OR operator
     *
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param string|int $value Value to compare
     * @return $this
     */
    public function orWhere(string $field, string $operator, string|int $value): static
    {
        $this->params[] = $value;

        $this->orWhere[] = "$field $operator ?";

        return $this;
    }

    /**
     * Add a WHERE NULL clause
     *
     * @param string $field Field name
     * @return $this
     */
    public function whereNull(string $field): static
    {
        $this->where[] = "$field IS NULL";
        
        return $this;
    }

    /**
     * Add a WHERE NOT NULL clause
     *
     * @param string $field Field name
     * @return $this
     */
    public function whereNotNull(string $field): static
    {
        $this->where[] = "$field IS NOT NULL";
        
        return $this;
    }

    /**
     * Add a WHERE BETWEEN clause
     *
     * @param string $field Field name
     * @param mixed $min Minimum value
     * @param mixed $max Maximum value
     * @return $this
     */
    public function whereBetween(string $field, $min, $max): static
    {
        $this->params[] = $min;
        $this->params[] = $max;
        
        $this->where[] = "$field BETWEEN ? AND ?";
        
        return $this;
    }

    /**
     * Add a WHERE NOT BETWEEN clause
     *
     * @param string $field Field name
     * @param mixed $min Minimum value
     * @param mixed $max Maximum value
     * @return $this
     */
    public function whereNotBetween(string $field, $min, $max): static
    {
        $this->params[] = $min;
        $this->params[] = $max;
        
        $this->where[] = "$field NOT BETWEEN ? AND ?";
        
        return $this;
    }

    /**
     * Add a WHERE clause for date comparison
     *
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param DateTimeInterface|string $date Date to compare
     * @return $this
     */
    public function whereDate(string $field, string $operator, DateTimeInterface|string $date): static
    {
        $dateStr = $date instanceof DateTimeInterface ? $date->format('Y-m-d') : $date;
        $this->params[] = $dateStr;
        
        $this->where[] = "DATE($field) $operator ?";
        
        return $this;
    }

    /**
     * Add a WHERE clause for month comparison
     *
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param int|string $month Month to compare (1-12)
     * @return $this
     */
    public function whereMonth(string $field, string $operator, int|string $month): static
    {
        $this->params[] = $month;
        
        $this->where[] = "MONTH($field) $operator ?";
        
        return $this;
    }

    /**
     * Add a WHERE clause for year comparison
     *
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param int|string $year Year to compare
     * @return $this
     */
    public function whereYear(string $field, string $operator, int|string $year): static
    {
        $this->params[] = $year;
        
        $this->where[] = "YEAR($field) $operator ?";
        
        return $this;
    }

    /**
     * Add a HAVING clause
     *
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param string|int $value Value to compare
     * @return $this
     */
    public function having(string $field, string $operator, string|int $value): static
    {
        $this->params[] = $value;

        $this->having[] = "$field $operator ?";

        return $this;
    }

    /**
     * Add a WHERE IN clause
     *
     * @param string $field Field name
     * @param array $values Values to compare
     * @return $this
     */
    public function whereIn(string $field, array $values): static
    {
        if (empty($values)) {
            return $this->whereRaw('1 = 0'); // Always false for empty IN clause
        }
        
        foreach ($values as $value) {
            $this->params[] = $value;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->whereIn[] = "$field IN ($placeholders)";

        return $this;
    }

    /**
     * Add a WHERE IN clause with OR operator
     *
     * @param string $field Field name
     * @param array $values Values to compare
     * @return $this
     */
    public function orWhereIn(string $field, array $values): static
    {
        if (empty($values)) {
            return $this;
        }
        
        foreach ($values as $value) {
            $this->params[] = $value;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->orWhereIn[] = "$field IN ($placeholders)";

        return $this;
    }

    /**
     * Add a WHERE NOT IN clause
     *
     * @param string $field Field name
     * @param array $values Values to compare
     * @return $this
     */
    public function whereNotIn(string $field, array $values): static
    {
        if (empty($values)) {
            return $this; // No effect for empty NOT IN clause
        }
        
        foreach ($values as $value) {
            $this->params[] = $value;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->whereNotIn[] = "$field NOT IN ($placeholders)";

        return $this;
    }

    /**
     * Add a WHERE clause with raw SQL
     *
     * @param string $sql Raw SQL for WHERE clause
     * @param array $bindings Parameters to be bound to the clause
     * @return $this
     */
    public function whereRaw(string $sql, array $bindings = []): static
    {
        if (!empty($bindings)) {
            foreach ($bindings as $binding) {
                $this->params[] = $binding;
            }
        }
        
        $this->where[] = $sql;
        
        return $this;
    }

    /**
     * Add a JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function join(string $table, string $first, string $operation, string $second): static
    {
        $this->join[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add an INNER JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function innerJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->innerJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add a LEFT JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function leftJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->leftJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add a RIGHT JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function rightJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->rightJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add a FULL JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function fullJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->fullJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add a CROSS JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function crossJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->crossJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add a FULL OUTER JOIN clause
     *
     * @param string $table Table to join
     * @param string $first First field
     * @param string $operation Comparison operator
     * @param string $second Second field
     * @return $this
     */
    public function fullOuterJoin(string $table, string $first, string $operation, string $second): static
    {
        $this->fullOuterJoin[] = "$table ON $first $operation $second";

        return $this;
    }

    /**
     * Add a LIMIT clause
     *
     * @param mixed $limit Maximum number of rows to return
     * @return $this
     */
    public function limit($limit): static
    {
        // Ensure the limit is an integer
        $limit = (int) $limit;
        
        $this->limit = " LIMIT " . $limit;

        return $this;
    }

    /**
     * Add an OFFSET clause
     *
     * @param mixed $offset Number of rows to skip
     * @return $this
     */
    public function offset($offset): static
    {
        // Ensure the offset is an integer
        $offset = (int) $offset;
        
        $this->offset = " OFFSET " . $offset;
        
        return $this;
    }

    /**
     * Alias for "limit" and "offset" methods
     *
     * @param mixed $offset Number of rows to skip
     * @param mixed $limit Maximum number of rows to return
     * @return $this
     */
    public function skip($offset, $limit = null): static
    {
        $this->offset($offset);
        
        if ($limit !== null) {
            $this->limit($limit);
        }
        
        return $this;
    }

    /**
     * Add an ORDER BY clause
     *
     * @param string $column Column to sort by
     * @param string $type Sort direction (ASC or DESC)
     * @return $this
     */
    public function orderBy(string $column, string $type = 'ASC'): static
    {
        $type = strtoupper($type) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy[] = $column . ' ' . $type;

        return $this;
    }

    /**
     * Add an ORDER BY DESC clause
     *
     * @param string $column Column to sort by
     * @return $this
     */
    public function orderByDesc(string $column): static
    {
        return $this->orderBy($column, 'DESC');
    }

    /**
     * Add a GROUP BY clause
     *
     * @param string $column Column to group by
     * @return $this
     */
    public function groupBy(string $column): static
    {
        $this->groupBy[] = $column;

        return $this;
    }

    /**
     * Get the first record from the result set
     *
     * @return object|bool Record object or false if not found
     * @throws Exception If query fails
     */
    public function first(): object|bool
    {
        try {
            $sql = $this->prepareQuery();
            
            $statement = $this->db->connect()->prepare($sql);
            $statement->execute($this->getFlattenedParams());
            
            return $statement->fetch();
        } catch (Exception $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Find a record by its ID
     *
     * @param int $id Record ID
     * @return mixed Record or null if not found
     * @throws Exception If query fails
     */
    public function findById(int $id): mixed
    {
        try {
            $table = $this->table;
            $sql = "SELECT * FROM $table WHERE id = ?";
            
            $statement = $this->db->connect()->prepare($sql);
            $statement->execute([$id]);
            
            return $statement->fetch();
        } catch (Exception $e) {
            throw new Exception("Failed to find record with ID $id: " . $e->getMessage());
        }
    }

    /**
     * Execute the query and get all results
     *
     * @return object Collection of records
     * @throws Exception If query fails
     */
    public function get(): object
    {
        try {
            $sql = $this->prepareQuery();
            
            $statement = $this->db->connect()->prepare($sql);
            $statement->execute($this->getFlattenedParams());
            
            return (object)$statement->fetchAll();
        } catch (Exception $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Prepare the SQL query with all clauses
     *
     * @return string Complete SQL query
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
        
        $sql .= $this->offsetQuery();
        
        // Apply raw expressions
        if (!empty($this->rawExpressions)) {
            $sql .= " " . implode(' ', $this->rawExpressions);
        }

        return $sql;
    }

    /**
     * Get flattened query parameters
     *
     * @return array Flattened parameters
     */
    private function getFlattenedParams(): array
    {
        return array_values(arrayFlatten($this->params));
    }

    /**
     * Get LIMIT clause
     *
     * @return string|null LIMIT clause or null
     */
    private function limitQuery(): ?string
    {
        if (isset($this->limit)) {
            return $this->limit;
        }

        return null;
    }
    
    /**
     * Get OFFSET clause
     *
     * @return string|null OFFSET clause or null
     */
    private function offsetQuery(): ?string
    {
        if (isset($this->offset)) {
            return $this->offset;
        }
        
        return null;
    }

    /**
     * Get GROUP BY clause
     *
     * @return string|null GROUP BY clause or null
     */
    private function groupByQuery(): ?string
    {
        if (!empty($this->groupBy)) {
            return " GROUP BY " . implode(', ', $this->groupBy);
        }

        return null;
    }

    /**
     * Get ORDER BY clause
     *
     * @return string|null ORDER BY clause or null
     */
    private function orderByQuery(): ?string
    {
        if (!empty($this->orderBy)) {
            return " ORDER BY " . implode(', ', $this->orderBy);
        }

        return null;
    }

    /**
     * Get WHERE IN clause
     *
     * @return string|null WHERE IN clause or null
     */
    private function whereInQuery(): ?string
    {
        if (!empty($this->whereIn)) {
            if (empty($this->where)) {
                return " WHERE " . implode(' AND ', $this->whereIn);
            } else {
                return " AND " . implode(' AND ', $this->whereIn);
            }
        }

        return null;
    }

    /**
     * Get OR WHERE IN clause
     *
     * @return string|null OR WHERE IN clause or null
     */
    private function orWhereInQuery(): ?string
    {
        if (empty($this->where) && empty($this->whereIn) && !empty($this->orWhereIn)) {
            return " WHERE " . implode(' OR ', $this->orWhereIn);
        } elseif (!empty($this->orWhereIn)) {
            return " OR " . implode(' OR ', $this->orWhereIn);
        }

        return null;
    }

    /**
     * Get WHERE NOT IN clause
     *
     * @return string|null WHERE NOT IN clause or null
     */
    private function whereNotInQuery(): ?string
    {
        if (!empty($this->whereNotIn)) {
            if (empty($this->where) && empty($this->whereIn) && empty($this->orWhereIn)) {
                return " WHERE " . implode(' AND ', $this->whereNotIn);
            } else {
                return " AND " . implode(' AND ', $this->whereNotIn);
            }
        }

        return null;
    }

    /**
     * Get WHERE clause
     *
     * @return string|null WHERE clause or null
     */
    private function whereQuery(): ?string
    {
        if (!empty($this->where)) {
            return " WHERE " . implode(' AND ', $this->where);
        }

        return null;
    }

    /**
     * Get OR WHERE clause
     *
     * @return string|null OR WHERE clause or null
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
     * Get HAVING clause
     *
     * @return string|null HAVING clause or null
     */
    private function havingQuery(): ?string
    {
        if (!empty($this->having)) {
            return " HAVING " . implode(' AND ', $this->having);
        }

        return null;
    }

    /**
     * Get all JOIN clauses
     *
     * @return string JOIN clauses
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
     * Get basic SELECT query for all records
     *
     * @return string Basic SELECT query
     */
    public function getAllDataWithOutConditions(): string
    {
        return "SELECT * FROM " . $this->table;
    }

    /**
     * Get paginated results
     *
     * @param int $pageLimit Maximum number of records per page
     * @return object Paginated records
     * @throws Exception If query fails
     */
    public function paginate(int $pageLimit = 10): object
    {
        try {
            $sql = $this->prepareQuery();
            
            $page = Request::get('page');
            $page = !isset($page) ? $this->startPage : (int)$page;
            
            if ($page > 0) {
                $startLimit = ($page * $pageLimit) - $pageLimit;
            } else {
                $startLimit = ($this->startPage * $pageLimit) - $pageLimit;
            }
            
            $totalRecord = $this->count();
            $totalPage = ceil($totalRecord / $pageLimit);
            
            $sql .= " LIMIT ? OFFSET ?";
            
            $this->params[] = $pageLimit;
            $this->params[] = $startLimit;
            
            $paginate = $this->db->connect()->prepare($sql);
            $paginate->execute($this->getFlattenedParams());
            
            $result = $paginate->fetchAll();
            
            $result['count'] = $totalRecord;
            $result['totalPage'] = $totalPage;
            $result['perPage'] = $pageLimit;
            $result['page'] = $page;
            $result['hasMorePages'] = $page < $totalPage;
            $result['hasPages'] = $totalPage > 1;
            $result['isFirstPage'] = $page === 1;
            $result['isLastPage'] = $page === $totalPage;
            
            return (object)$result;
        } catch (Exception $e) {
            throw new Exception("Pagination failed: " . $e->getMessage());
        }
    }

    /**
     * Count records in the result set
     *
     * @return int Number of records
     * @throws Exception If query fails
     */
    public function count(): int
    {
        try {
            $sql = $this->prepareQuery();
            
            $statement = $this->db->connect()->prepare($sql);
            $statement->execute($this->getFlattenedParams());
            
            return $statement->rowCount();
        } catch (Exception $e) {
            throw new Exception("Count query failed: " . $e->getMessage());
        }
    }

    /**
     * Helper method for creating placeholders for IN clauses
     *
     * @param array $values Array of values
     * @return string Comma-separated list of placeholders
     */
    private function createParameterPlaceholders(array $values): string
    {
        return implode(',', array_fill(0, count($values), '?'));
    }
}