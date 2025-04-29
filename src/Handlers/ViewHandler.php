<?php

namespace Src\Handlers;

use App\Interfaces\ViewInterface;

class ViewHandler implements ViewInterface
{
    /**
     * Send a view to the browser
     * 
     * @param string $name View name using dot notation (example: users.index)
     * @param array $data Data to pass to the view
     * @return void
     */
    public function send(string $name, array $data = []): void
    {
        // Extract variables from data array
        extract($data);
        
        // Convert dot notation to directory structure (users.index => users/index)
        $viewPath = str_replace('.', '/', strtolower($name));
        
        // Build the full path to the view file
        $viewFilePath = __DIR__ . '/../../view/' . $viewPath . '.php';
        
        // Check if view file exists
        if (!file_exists($viewFilePath)) {
            throw new \Exception("View file not found: {$viewFilePath}");
        }
        
        // Include the view file
        require $viewFilePath;
    }
}