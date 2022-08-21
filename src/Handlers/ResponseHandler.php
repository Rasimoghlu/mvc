<?php

namespace Src\Handlers;

use App\Interfaces\ResponseInterface;

class ResponseHandler implements ResponseInterface
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