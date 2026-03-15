<?php

namespace App\Traits;

trait CrudTrait
{
    public function create(array $data): mixed
    {
        $keys = $this->getArrayKeysFromRequestForCreate($data);
        $placeholders = rtrim(str_repeat('?,', count($data)), ',');

        $sql = "INSERT INTO {$this->table} ($keys) VALUES ($placeholders)";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(array_values($data));

        $lastInsertedId = $this->db->connect()->lastInsertId();

        return $this->getLastInsertedRow($lastInsertedId);
    }

    public function update(array $data): bool
    {
        $updateData = $data;

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

        return $stmt->execute(array_merge(array_values($updateData), array_values($whereParams)));
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->whereQuery();
        $sql .= $this->whereInQuery();

        $stmt = $this->db->connect()->prepare($sql);

        return $stmt->execute(is_array($this->params) ? array_values($this->params) : [$this->params]);
    }

    public function getLastInsertedRow(int $lastInsertedId): mixed
    {
        return $this->findById($lastInsertedId);
    }

    private function getArrayKeysFromRequestForCreate(array $data): string
    {
        return implode(', ', array_keys($data));
    }

    private function generatePlaceholders(int $count): string
    {
        return rtrim(str_repeat('?,', $count), ',');
    }
}
