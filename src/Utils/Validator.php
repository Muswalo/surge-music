<?php

namespace Muswalo\Surgemusic\Utils;

use Exception;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    /**
     * Validator constructor.
     *
     * @param array $data - The data to validate.
     * @param array $rules - Validation rules in the format.
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Run validation.
     *
     * @throws Exception If validation fails.
     */
    public function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        if (!empty($this->errors)) {
            throw new Exception('Validation failed: ' . json_encode($this->errors));
        }
    }

    /**
     * Apply a specific validation rule to a field.
     *
     * @param string $field
     * @param string $rule
     */
    protected function applyRule(string $field, string $rule): void
    {
        [$ruleName, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

        $value = $this->data[$field] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'The field is required.');
                }
                break;

            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, 'The field must be a string.');
                }
                break;

            case 'integer':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, 'The field must be an integer.');
                }
                break;

            case 'max':
                if (is_string($value) && strlen($value) > (int)$parameter) {
                    $this->addError($field, "The field must not exceed {$parameter} characters.");
                }
                break;

            case 'min':
                if (is_string($value) && strlen($value) < (int)$parameter) {
                    $this->addError($field, "The field must be at least {$parameter} characters.");
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'The field must be a valid email address.');
                }
                break;

            case 'in':
                $allowedValues = explode(',', $parameter);
                if (!in_array($value, $allowedValues)) {
                    $this->addError($field, 'The field must be one of: ' . implode(', ', $allowedValues) . '.');
                }
                break;


            default:
                $this->addError($field, "The rule '{$ruleName}' is not supported.");
        }
    }

    /**
     * Add an error for a field.
     *
     * @param string $field
     * @param string $message
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
