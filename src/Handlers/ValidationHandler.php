<?php

namespace Src\Handlers;

use App\Interfaces\ValidationInterface;
use Src\Facades\Session;

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
    private function rules(array $data, array $validationRules)
    {
        foreach ($validationRules as $field => $rules) {
            foreach (explode('|', $rules) as $rule) {
                $this->requiredValidation($rule, $field, $data);

                if (array_key_exists($field, $data)) {
                    $this->stringValidation($rule, $field, $data);
                    $this->integerValidation($rule, $field, $data);
                    $this->emailValidation($rule, $field, $data);
                    $this->phoneValidation($rule, $field, $data);
                    $this->alphaNumericValidation($rule, $field, $data);
                }
            }
        }

        if (count($this->errors)) {
            return $this->returnBackWithValidationErrors();
        }

        return $data;

    }

    public function returnBackWithValidationErrors()
    {
        Session::set('errors', $this->errors);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die();
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
            $this->errors[$field] = "The " . $field . " is required.";
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
        if ($rule == 'string' && !preg_match("/^[A-Za-z0-9_-]*$/", $data[$field])) {
            $this->errors[$field] = "The " . $field . " field must be a string.";
        }
    }

    /**
     * @param $rule
     * @param $data
     * @param $field
     * @return void
     */
    private function integerValidation($rule, $field, $data): void
    {
        if ($rule == 'integer' && !preg_match("/^[1-9][0-9]{0,15}$/", $data[$field])) {
            $this->errors[$field] = "The " . $field . " field must be a integer.";
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
        if ($rule == 'email' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $data[$field])) {
            $this->errors[$field] = "The " . $field . " field must be a valid email.";
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
            $this->errors[$field] = "The " . $field . " field must be a valid phone number.";
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
            $this->errors[$field] = "The " . $field . " field must be a valid alphanumeric.";
        }
    }

}