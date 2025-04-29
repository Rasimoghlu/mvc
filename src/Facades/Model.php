<?php

namespace Src\Facades;

use App\Http\Exceptions\MethodNotFoundException;
use App\Http\Exceptions\ModelNotFoundException;
use DateTime;
use Exception;
use Src\Handlers\QueryBuilderHandler;

/**
 * Base Model Class
 * 
 * Provides common functionality for all models in the application.
 * Handles database interactions through QueryBuilderHandler.
 */
class Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected string $table = '';

    /**
     * The primary key for the model
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Fields that can be mass assigned
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * Fields that should be hidden from arrays and JSON
     *
     * @var array
     */
    protected array $hidden = [];

    /**
     * Whether to use timestamps (created_at, updated_at)
     *
     * @var bool
     */
    protected bool $timestamps = true;

    /**
     * Field name for created timestamp
     *
     * @var string
     */
    protected string $createdField = 'created_at';

    /**
     * Field name for updated timestamp
     *
     * @var string
     */
    protected string $updatedField = 'updated_at';

    /**
     * Query builder instance
     *
     * @var QueryBuilderHandler
     */
    private QueryBuilderHandler $handler;

    /**
     * Model constructor
     */
    public function __construct()
    {
        $this->handler = new QueryBuilderHandler($this->getTable());
    }

    /**
     * Get all records from the model's table
     *
     * @return array|object Collection of records
     */
    public static function all(): array|object
    {
        return (new static())->handler->get();
    }

    /**
     * Find a record by its primary key
     *
     * @param int $id Primary key value
     * @return object|null Record if found, null otherwise
     */
    public static function find(int $id): ?object
    {
        return (new static())->handler->where((new static())->primaryKey, '=', $id)->first();
    }

    /**
     * Find a record by its primary key or throw an exception if it doesn't exist
     *
     * @param int $id Primary key value
     * @return object Record if found
     * @throws ModelNotFoundException
     */
    public static function findOrFail(int $id): object
    {
        $result = static::find($id);

        if (!$result) {
            throw new ModelNotFoundException("No query results for model [" . static::class . "] {$id}");
        }

        return $result;
    }

    /**
     * Delete a record by its primary key
     *
     * @param int $id Primary key value
     * @return bool Success status
     */
    public static function delete(int $id): bool
    {
        $instance = new static();
        return $instance->handler->where($instance->primaryKey, '=', $id)->delete();
    }

    /**
     * Update a record by its primary key
     *
     * @param int $id Primary key value
     * @param array $data Data to update
     * @return bool Success status
     */
    public static function update(int $id, array $data): bool
    {
        $instance = new static();
        
        // Filter data based on fillable fields if defined
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }
        
        // Add timestamps if enabled and column exists
        if ($instance->timestamps) {
            try {
                $instance->handler->db->connect()->query("SHOW COLUMNS FROM {$instance->table} LIKE '{$instance->updatedField}'");
                if ($instance->handler->db->connect()->rowCount() > 0) {
                    $data[$instance->updatedField] = (new DateTime())->format('Y-m-d H:i:s');
                }
            } catch (Exception $e) {
                // Column doesn't exist, just continue without timestamp
            }
        }
        
        return $instance->handler->where($instance->primaryKey, '=', $id)->update($data);
    }

    /**
     * Create a new record
     *
     * @param array $data Data for the new record
     * @return object|null Created record
     */
    public static function create(array $data): ?object
    {
        $instance = new static();
        
        // Filter data based on fillable fields if defined
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }
        
        // Add timestamps if enabled and columns exist
        if ($instance->timestamps) {
            $now = (new DateTime())->format('Y-m-d H:i:s');
            
            try {
                // Check if created_at column exists
                $instance->handler->db->connect()->query("SHOW COLUMNS FROM {$instance->table} LIKE '{$instance->createdField}'");
                if ($instance->handler->db->connect()->rowCount() > 0) {
                    $data[$instance->createdField] = $now;
                }
                
                // Check if updated_at column exists
                $instance->handler->db->connect()->query("SHOW COLUMNS FROM {$instance->table} LIKE '{$instance->updatedField}'");
                if ($instance->handler->db->connect()->rowCount() > 0) {
                    $data[$instance->updatedField] = $now;
                }
            } catch (Exception $e) {
                // Columns don't exist, just continue without timestamps
            }
        }
        
        try {
            return $instance->handler->create($data);
        } catch (Exception $e) {
            error_log('Error creating record: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Start a query where clause
     *
     * @param string $field Column name
     * @param string $operator Comparison operator
     * @param mixed $value Value to compare against
     * @return QueryBuilderHandler
     */
    public static function where(string $field, string $operator, $value): QueryBuilderHandler
    {
        $instance = new static();
        return $instance->handler->where($field, $operator, $value);
    }

    /**
     * Count records that match the query conditions
     *
     * @return int Number of matching records
     */
    public static function count(): int
    {
        return (new static())->handler->count();
    }
    
    /**
     * Get the first record that matches the query conditions
     *
     * @return object|null First matching record
     */
    public static function first(): ?object
    {
        return (new static())->handler->first();
    }
    
    /**
     * Paginate query results
     *
     * @param int $perPage Number of items per page
     * @return object Paginated results
     */
    public static function paginate(int $perPage = 15): object
    {
        return (new static())->handler->paginate($perPage);
    }

    /**
     * Handle dynamic static method calls
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return (new static())->$name(...$arguments);
    }

    /**
     * Handle dynamic method calls
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     * @throws MethodNotFoundException
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->handler, $name)) {
            return $this->handler->$name(...$arguments);
        }

        throw new MethodNotFoundException("Method [{$name}] does not exist on model [" . static::class . "]");
    }

    /**
     * Get the table associated with the model
     *
     * @return string Table name
     */
    public function getTable(): string
    {
       return $this->table ?? strtolower(class_basename($this) . 's');
    }

    /**
     * Set the table associated with the model
     *
     * @param string $table Table name
     * @return $this
     */
    public function setTable(string $table): static
    {
        $this->table = $table;

        return $this;
    }
    
    /**
     * Hide specific attributes from array and JSON output
     *
     * @param array $attributes Attributes to hide
     * @return $this
     */
    public function hideAttributes(array $attributes): static
    {
        $this->hidden = array_merge($this->hidden, $attributes);
        
        return $this;
    }
    
    /**
     * Convert model data to array, respecting hidden attributes
     *
     * @param object $data Data to convert
     * @return array
     */
    public function toArray(object $data): array
    {
        $array = (array) $data;
        
        foreach ($this->hidden as $attribute) {
            if (isset($array[$attribute])) {
                unset($array[$attribute]);
            }
        }
        
        return $array;
    }
}