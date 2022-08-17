<?php

namespace App\Traits;

trait CrudTrait
{
    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
         cleanSql($data);

        $keys = $this->getArrayKeysFromRequestForCreate($data);

        $values = $this->getDataFromRequest($data);

        $sql = "INSERT INTO $this->table ($keys) VALUES ($values)";

        $this->db->connect()->prepare("$sql")->execute(array_values($data));

        $lastInsertedId = $this->db->connect()->lastInsertId();

        return $this->getLastInsertedRow($lastInsertedId);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function update(array $data): mixed
    {
        cleanSql($data);

        $data[] = $this->params;

        $keys = $this->getArrayKeysFromRequestForUpdate($data);

        $sql = "UPDATE $this->table SET $keys";

        $sql .= $this->whereQuery();

        $sql .= $this->whereInQuery();

        $this->db->connect()->prepare("$sql")->execute(array_values(arrayFlatten($data)));

        $lastInsertedId = $this->db->connect()->lastInsertId();

        return $this->getLastInsertedRow($lastInsertedId);
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $sql = "DELETE FROM $this->table";

        $sql .= $this->whereQuery();

        $sql .= $this->whereInQuery();

        $this->db->connect()->prepare("$sql")->execute(array_values($this->params));
    }

    /**
     * @param int $lastInsertedId
     * @return mixed
     */
    public function getLastInsertedRow(int $lastInsertedId): mixed
    {
        return $this->findById($lastInsertedId);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getDataFromRequest(array $data): string
    {
        return $this->implodeArrayDataAndReplaceWithQuestionMark($data);
    }

    /**
     * @param array $data
     * @return string
     */
    private function implodeArrayDataAndReplaceWithQuestionMark(array $data): string
    {
        $values = [];
        foreach ($data as $value) {
            $values[] = str_replace($value, '?', $value);
        }

        return implode(', ', $values);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getArrayValuesFromRequest(array $data): string
    {
        return implode(', ', array_values($data));
    }

    /**
     * @param array $data
     * @return string
     */
    private function getArrayKeysFromRequestForCreate(array $data): string
    {
        return implode(', ', array_keys($data));
    }

    /**
     * @param array $data
     * @return string
     */
    private function getArrayKeysFromRequestForUpdate(array $data): string
    {
        $arr = [];

        foreach ($data as $key => $value) {
            if ($key != 0) {
                $arr[] = $key . ' = ?';
            }
        }

        return implode(', ', $arr);
    }
}