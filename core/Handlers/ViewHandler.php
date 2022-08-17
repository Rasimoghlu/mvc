<?php

namespace Core\Handlers;

use App\Interfaces\ViewInterface;

class ViewHandler implements ViewInterface
{
    /**
     * @param string $name
     * @param array $data
     * @return void
     */
    public function send(string $name, array $data = []): void
    {
        extract($data);

        require __DIR__ . '/../../view/' . strtolower($name) . '.php';
    }

}