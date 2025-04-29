<?php

namespace App\Traits;

trait CrudTrait
{
    /**
     * Create a new record
     * 
     * @param array $data Data for creating the record
     * @return mixed The created record
     */
    public function create(array $data): mixed
    {
        $data = cleanSql($data);

        $keys = $this->getArrayKeysFromRequestForCreate($data);
        $placeholders = rtrim(str_repeat('?,', count($data)), ',');

        $sql = "INSERT INTO {$this->table} ($keys) VALUES ($placeholders)";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(array_values($data));

        $lastInsertedId = $this->db->connect()->lastInsertId();

        return $this->getLastInsertedRow($lastInsertedId);
    }

    /**
     * Update an existing record
     * 
     * @param array $data Data for updating the record
     * @return bool Success status of the update operation
     */
    public function update(array $data): bool
    {
        $data = cleanSql($data);
        $updateData = $data;
        
        // Add where params to the end of the data array
        $whereParams = is_array($this->params) ? $this->params : [$this->params];
        
        $setClause = '';
        foreach (array_keys($updateData) as $key) {
            $setClause .= "$key = ?, ";
        }
        $setClause = rtrim($setClause, ', ');

        $sql = "UPDATE {$this->table} SET $setClause";
        $sql .= $this->whereQuery();
        $sql .= $this->whereInQuery();

        $stmt = $this->db->connect()->prepare($sql);
        $result = $stmt->execute(array_merge(array_values($updateData), array_values($whereParams)));
        
        return $result;
    }

    /**
     * Delete a record
     * 
     * @return bool
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->whereQuery();
        $sql .= $this->whereInQuery();

        $stmt = $this->db->connect()->prepare($sql);
        $result = $stmt->execute(is_array($this->params) ? array_values($this->params) : [$this->params]);
        
        return $result;
    }

    /**
     * Get the record that was just inserted
     * 
     * @param int $lastInsertedId ID of the last inserted record
     * @return mixed The inserted record
     */
    public function getLastInsertedRow(int $lastInsertedId): mixed
    {
        return $this->findById($lastInsertedId);
    }

    /**
     * Get array keys formatted for SQL INSERT statement
     * 
     * @param array $data Input data
     * @return string Comma-separated column names
     */
    private function getArrayKeysFromRequestForCreate(array $data): string
    {
        return implode(', ', array_keys($data));
    }

    /**
     * Generate placeholders for prepared statements
     * 
     * @param int $count Number of placeholders
     * @return string Comma-separated placeholders
     */
    private function generatePlaceholders(int $count): string
    {
        return rtrim(str_repeat('?,', $count), ',');
    }
}