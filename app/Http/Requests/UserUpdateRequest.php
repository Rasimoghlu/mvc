<?php

namespace App\Http\Requests;

use Src\Handlers\ValidationHandler;

/**
 * UserUpdateRequest
 * 
 * Validates user update requests
 */
class UserUpdateRequest
{
    /**
     * Validation handler
     * 
     * @var ValidationHandler
     */
    protected ValidationHandler $validator;
    
    /**
     * Validated data
     * 
     * @var array|null
     */
    protected ?array $validatedData = null;
    
    /**
     * Create a new UserUpdateRequest instance
     */
    public function __construct()
    {
        $this->validator = new ValidationHandler();
    }
    
    /**
     * Get the validation rules for user update
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|min:3|max:100',
            'email' => 'sometimes|email',
            'password' => 'sometimes|min:8'
        ];
    }
    
    /**
     * Validate the request
     * 
     * @return bool
     */
    public function validate(): bool
    {
        $this->validatedData = $this->validator->make($_POST, $this->rules());
        
        return $this->validatedData !== false;
    }
    
    /**
     * Get validated data
     * 
     * @return array|null
     */
    public function validated(): ?array
    {
        return $this->validatedData;
    }
    
    /**
     * Handle failed validation
     * 
     * @return mixed
     */
    public function failedValidation()
    {
        return $this->validator->returnBackWithValidationErrors();
    }
} 