<?php

namespace App\Http\Requests;

use Src\Handlers\ValidationHandler;

class TaskUpdateRequest
{
    protected ValidationHandler $validator;
    protected ?array $validatedData = null;

    public function __construct()
    {
        $this->validator = new ValidationHandler();
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|min:3',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:pending,in_progress,done',
        ];
    }

    public function validate(): bool
    {
        $this->validatedData = $this->validator->make($_POST, $this->rules());
        return $this->validatedData !== false;
    }

    public function validated(): ?array
    {
        return $this->validatedData;
    }

    public function failedValidation()
    {
        return $this->validator->returnBackWithValidationErrors();
    }
}
