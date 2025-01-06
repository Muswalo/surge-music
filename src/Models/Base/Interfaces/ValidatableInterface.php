<?php

namespace Muswalo\Surgemusic\Models\Base\Interfaces;

interface ValidatableInterface 
{
    public function validate(): bool;
    public function getValidationRules(): array;
    public function setValidationRules(array $rules): void;
    public function addValidationRule(string $field, string $rule): void;
}