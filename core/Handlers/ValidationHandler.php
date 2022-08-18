<?php

namespace Core\Handlers;

use App\Interfaces\ValidationInterface;

class ValidationHandler implements ValidationInterface
{
    /**
     * @var array
     */
    public array $errors = [];

    /**
     * @param array $data
     * @param array $rules
     * @return array
     */
    public function make(array $data, array $rules)
    {
       return $this->rules($data, $rules);
    }

    /**
     * @param array $data
     * @param array $validationRules
     * @return array
     */
    private function rules(array $data, array $validationRules): array
    {
            foreach ($validationRules as $field => $rules)
            {
                foreach (explode('|', $rules) as $rule) {
                    $this->requiredValidation($rule, $field, $data);

                    if (array_key_exists($field, $data)){
                        $this->stringValidation($rule, $field, $data);
                        $this->emailValidation($rule, $field, $data);
                        $this->phoneValidation($rule, $field, $data);
                        $this->alphaNumericValidation($rule, $field, $data);
                    }
                }
            }

            return $this->errors;
    }

    /**
     * @param $rule
     * @param $data
     * @param $field
     * @return void
     */
    private function requiredValidation($rule, $field, $data): void
    {
        if ($rule == 'required' && empty($data[$field])) {
            $this->errors[] = "The " . $field ." is required.";
        }
    }

    /**
     * @param $rule
     * @param $data
     * @param $field
     * @return void
     */
    private function stringValidation($rule, $field, $data): void
    {
        if ($rule == 'string' && !preg_match("/P[A-Z]P/", $data[$field])) {
            $this->errors[] = "The " . $field . " field must be a string.";
        }
    }

    /**
     * @param $rule
     * @param $data
     * @param $field
     * @return void
     */
    private function emailValidation($rule, $field, $data): void
    {
        if ($rule == 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "The " . $field . " field must be a valid email.";
        }
    }

    /**
     * @param $rule
     * @param $data
     * @param $field
     * @return void
     */
    private function phoneValidation($rule, $field, $data): void
    {
        if ($rule == 'phone' && !preg_match('/^[0-9 \-\(\)\+]+$/i', $data[$field])) {
            $this->errors[] = "The " . $field . " field must be a valid phone number.";
        }
    }

    /**
     * @param $rule
     * @param $data
     * @param $field
     * @return void
     */
    private function alphaNumericValidation($rule, $field, $data): void
    {
        if ($rule == 'alphanumeric' && !preg_match('/^[a-z0-9 .\-]+$/i', $data[$field])) {
            $this->errors[] = "The " . $field . " field must be a valid alphanumeric.";
        }
    }

}