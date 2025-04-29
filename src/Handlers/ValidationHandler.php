<?php

namespace Src\Handlers;

use App\Interfaces\ValidationInterface;
use Src\Facades\Session;

class ValidationHandler implements ValidationInterface
{
    /**
     * Validation errors
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Validation data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Error messages
     * 
     * @var array
     */
    protected array $messages = [
        'required' => 'The :field field is required.',
        'string' => 'The :field field must be a string.',
        'integer' => 'The :field field must be an integer.',
        'numeric' => 'The :field field must be a number.',
        'email' => 'The :field field must be a valid email address.',
        'min' => 'The :field field must be at least :param characters.',
        'max' => 'The :field field may not be greater than :param characters.',
        'between' => 'The :field field must be between :min and :max.',
        'in' => 'The selected :field is invalid.',
        'unique' => 'The :field has already been taken.',
        'regex' => 'The :field format is invalid.',
        'date' => 'The :field is not a valid date.',
        'alpha' => 'The :field field may only contain letters.',
        'alpha_num' => 'The :field field may only contain letters and numbers.',
        'alpha_dash' => 'The :field field may only contain letters, numbers, dashes and underscores.',
        'boolean' => 'The :field field must be true or false.',
        'confirmed' => 'The :field confirmation does not match.',
        'different' => 'The :field and :param must be different.',
        'same' => 'The :field and :param must match.',
        'url' => 'The :field format is invalid.',
        'ip' => 'The :field must be a valid IP address.',
        'json' => 'The :field must be a valid JSON string.',
        'phone' => 'The :field must be a valid phone number.',
    ];

    /**
     * Available validation rules patterns
     * 
     * @var array
     */
    protected array $patterns = [
        'email' => '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i',
        'alpha' => '/^[a-zA-Z]+$/',
        'alpha_num' => '/^[a-zA-Z0-9]+$/',
        'alpha_dash' => '/^[a-zA-Z0-9_-]+$/',
        'numeric' => '/^[0-9]+$/',
        'integer' => '/^-?[0-9]+$/',
        'natural' => '/^[0-9]+$/',
        'natural_non_zero' => '/^[1-9][0-9]*$/',
        'phone' => '/^[0-9 \-\(\)\+]+$/i',
        'date' => '/^\d{4}-\d{2}-\d{2}$/',
        'url' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        'ip' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
        'json' => '/^(?:{\s*(?:"[^"]*":\s*(?:"[^"]*"|true|false|null|\d+|\[[^\]]*\]|\{[^}]*\}),?\s*)*}|\[[^\]]*\])$/'
    ];

    /**
     * Validate data with provided rules
     *
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array|bool Validated data or false if validation fails
     */
    public function make(array $data, array $rules): array|bool
    {
        $this->data = $data;
        $this->errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $this->validateField($field, $ruleString);
        }

        // Return data if validation passes, otherwise store errors and return false
        if (empty($this->errors)) {
            return $this->data;
        }

        // Store errors in session
        $this->storeErrors();
        
        return false;
    }
    
    /**
     * Validate a specific field with its rules
     * 
     * @param string $field Field name
     * @param string $ruleString Rules string
     * @return void
     */
    protected function validateField(string $field, string $ruleString): void
    {
        $rules = explode('|', $ruleString);
        
        // Check if field is required or exists before other validations
        $isRequired = in_array('required', $rules);
        $fieldExists = isset($this->data[$field]) && $this->data[$field] !== '';
        
        // Skip validation if field is not required and does not exist
        if (!$isRequired && !$fieldExists) {
            return;
        }
        
        // Check required rule first
        if ($isRequired && !$fieldExists) {
            $this->addError($field, 'required');
            return; // Skip other validations if required field is missing
        }
        
        // Apply all other rules if field exists
        if ($fieldExists) {
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }
    }
    
    /**
     * Apply a specific rule to a field
     * 
     * @param string $field Field name
     * @param string $rule Rule to apply
     * @return void
     */
    protected function applyRule(string $field, string $rule): void
    {
        // Skip if already has error or if it's the 'required' rule (already checked)
        if (isset($this->errors[$field]) || $rule === 'required') {
            return;
        }
        
        // Extract rule name and parameters
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }
        
        $value = $this->data[$field];
        
        switch ($rule) {
            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, $rule);
                }
                break;
                
            case 'integer':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, $rule);
                }
                break;
                
            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, $rule);
                }
                break;
                
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $rule);
                }
                break;
                
            case 'min':
                if (isset($params[0]) && strlen($value) < $params[0]) {
                    $this->addError($field, $rule, ['param' => $params[0]]);
                }
                break;
                
            case 'max':
                if (isset($params[0]) && strlen($value) > $params[0]) {
                    $this->addError($field, $rule, ['param' => $params[0]]);
                }
                break;
                
            case 'between':
                if (isset($params[0], $params[1]) && (strlen($value) < $params[0] || strlen($value) > $params[1])) {
                    $this->addError($field, $rule, ['min' => $params[0], 'max' => $params[1]]);
                }
                break;
                
            case 'in':
                if (!empty($params) && !in_array($value, $params)) {
                    $this->addError($field, $rule);
                }
                break;
                
            case 'regex':
                if (isset($params[0]) && !preg_match($params[0], $value)) {
                    $this->addError($field, $rule);
                }
                break;
                
            case 'confirmed':
                $confirmation = $field . '_confirmation';
                if (!isset($this->data[$confirmation]) || $value !== $this->data[$confirmation]) {
                    $this->addError($field, $rule);
                }
                break;
                
            default:
                // Check if rule has a pattern
                if (isset($this->patterns[$rule]) && !preg_match($this->patterns[$rule], $value)) {
                    $this->addError($field, $rule);
                }
                break;
        }
    }
    
    /**
     * Add an error message for a field
     * 
     * @param string $field Field name
     * @param string $rule Failed rule
     * @param array $params Parameters for message
     * @return void
     */
    protected function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->messages[$rule] ?? "The $field field is invalid.";
        
        // Replace placeholders in the message
        $message = str_replace(':field', $field, $message);
        
        foreach ($params as $key => $value) {
            $message = str_replace(":$key", $value, $message);
        }
        
        $this->errors[$field] = $message;
    }
    
    /**
     * Store validation errors in session
     * 
     * @return void
     */
    protected function storeErrors(): void
    {
        Session::set('errors', $this->errors);
        
        // Store old input data for form repopulation
        if (!empty($this->data)) {
            Session::set('old', $this->data);
        }
    }
    
    /**
     * Get all validation errors
     * 
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
    
    /**
     * Check if validation has errors
     * 
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Check if validation passes
     * 
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Redirect back with validation errors
     * 
     * @return never
     */
    public function returnBackWithValidationErrors()
    {
        $this->storeErrors();
        
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
}