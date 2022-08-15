<?php

namespace Core\Handlers;

class ResponseHandler
{
    /**
     * @param $data
     * @return false|string
     */
    public function json($data): bool|string
    {
        return json_encode($data);
    }
}