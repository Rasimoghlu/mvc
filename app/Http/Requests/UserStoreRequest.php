<?php

namespace App\Http\Requests;

use Src\Handlers\ValidationHandler;

/**
 * UserStoreRequest
 * 
 * Validates user store requests
 */
class UserStoreRequest
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
     * Create a new UserStoreRequest instance
     */
    public function __construct()
    {
        $this->validator = new ValidationHandler();
    }
    
    /**
     * Get the validation rules for user store
     * 
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8'
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