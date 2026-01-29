<?php

declare(strict_types=1);

namespace FormForge\Validation;

use FormForge\Contracts\ValidatorInterface;
use FormForge\Contracts\RuleInterface;
use FormForge\Validation\Rules\Required;
use FormForge\Validation\Rules\Email;
use FormForge\Validation\Rules\Numeric;
use FormForge\Validation\Rules\Min;
use FormForge\Validation\Rules\Max;
use FormForge\Validation\Rules\In;
use FormForge\Validation\Rules\Regex;

/**
 * Form validator with support for rule objects and rule strings
 */
class Validator implements ValidatorInterface
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $customMessages = [];
    protected bool $validated = false;

    public static function make(array $data, array $rules, array $messages = []): static
    {
        $validator = new static();
        $validator->data = $data;
        $validator->rules = $rules;
        $validator->customMessages = $messages;
        return $validator;
    }

    public function validate(): bool
    {
        if ($this->validated) {
            return empty($this->errors);
        }

        $this->errors = [];
        $this->validated = true;

        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rules = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($rules as $rule) {
                $ruleInstance = $this->resolveRule($rule);

                if ($ruleInstance && !$ruleInstance->passes($value, $this->data)) {
                    $message = $this->customMessages[$field] ?? $ruleInstance->message();
                    $this->errors[$field][] = $message;
                    break; // Stop on first error for this field
                }
            }
        }

        return empty($this->errors);
    }

    public function passes(): bool
    {
        return $this->validate();
    }

    public function fails(): bool
    {
        return !$this->validate();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function validated(): array
    {
        if (!$this->validated) {
            $this->validate();
        }

        // Return only the data that passed validation (fields with rules and no errors)
        $validated = [];
        foreach ($this->rules as $field => $fieldRules) {
            if (!isset($this->errors[$field])) {
                $validated[$field] = $this->data[$field] ?? null;
            }
        }
        return $validated;
    }

    protected function resolveRule(mixed $rule): ?RuleInterface
    {
        // Already a rule instance
        if ($rule instanceof RuleInterface) {
            return $rule;
        }

        // String rule: parse it
        if (is_string($rule)) {
            $parts = explode(':', $rule, 2);
            $ruleName = $parts[0];
            $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

            return match ($ruleName) {
                'required' => new Required(),
                'email' => new Email(),
                'numeric' => new Numeric(),
                'min' => new Min((float)($params[0] ?? 0)),
                'max' => new Max((float)($params[0] ?? PHP_INT_MAX)),
                'in' => new In($params),
                'regex' => new Regex($params[0] ?? '//'),
                default => null,
            };
        }

        return null;
    }
}
