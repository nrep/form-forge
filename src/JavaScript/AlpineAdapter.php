<?php

declare(strict_types=1);

namespace FormForge\JavaScript;

use FormForge\Contracts\FormInterface;
use FormForge\Contracts\FieldInterface;

/**
 * Alpine.js adapter for generating reactive form state
 */
class AlpineAdapter
{
    protected ?FormInterface $form = null;
    protected array $customHandlers = [];
    protected array $watchers = [];

    public static function make(FormInterface $form): static
    {
        $adapter = new static();
        $adapter->form = $form;
        return $adapter;
    }

    public function handler(string $name, string $code): static
    {
        $this->customHandlers[$name] = $code;
        return $this;
    }

    public function watch(string $field, string $callback): static
    {
        $this->watchers[$field] = $callback;
        return $this;
    }

    /**
     * Generate Alpine x-data object for the form
     */
    public function toXData(): string
    {
        if ($this->form === null) {
            throw new \RuntimeException('Form not set. Use AlpineAdapter::make() to create an instance.');
        }

        $data = [
            'form' => $this->buildFormState(),
            'errors' => $this->buildErrorsState(),
            'loading' => false,
            'submitted' => false,
        ];

        // Add computed visibility states
        $data = array_merge($data, $this->buildVisibilityStates());

        // Add custom handlers
        foreach ($this->customHandlers as $name => $code) {
            $data[$name] = '__FUNCTION__' . $code . '__ENDFUNCTION__';
        }

        // Add watchers
        if (!empty($this->watchers)) {
            $data['init'] = $this->buildInitWithWatchers();
        }

        return $this->encodeWithFunctions($data);
    }

    /**
     * Generate form state with default values
     */
    protected function buildFormState(): array
    {
        $state = [];
        foreach ($this->form->getFields() as $field) {
            $name = $field->getName();
            $default = $field->getDefault();
            $state[$name] = $default;
        }
        return $state;
    }

    /**
     * Generate errors state
     */
    protected function buildErrorsState(): array
    {
        $errors = [];
        foreach ($this->form->getFields() as $field) {
            $errors[$field->getName()] = null;
        }
        return $errors;
    }

    /**
     * Build visibility states for conditional fields
     */
    protected function buildVisibilityStates(): array
    {
        $states = [];
        foreach ($this->form->getFields() as $field) {
            $data = $field->toArray();
            if (!empty($data['showWhen'])) {
                $condition = $data['showWhen'];
                $states['show_' . $field->getName()] = '__FUNCTION__() { return this.form.' . $condition['field'] . ' ' . $condition['operator'] . ' ' . json_encode($condition['value']) . '; }__ENDFUNCTION__';
            }
        }
        return $states;
    }

    /**
     * Build init function with watchers
     */
    protected function buildInitWithWatchers(): string
    {
        $watchCode = '';
        foreach ($this->watchers as $field => $callback) {
            $watchCode .= "\$watch('form.{$field}', {$callback});\n";
        }

        return '__FUNCTION__() { ' . $watchCode . ' }__ENDFUNCTION__';
    }

    /**
     * Encode data with proper function handling
     */
    protected function encodeWithFunctions(array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);

        // Replace function placeholders with actual functions
        $json = preg_replace(
            '/"__FUNCTION__(.+?)__ENDFUNCTION__"/s',
            '$1',
            $json
        );

        return $json;
    }

    /**
     * Generate validation code for client-side validation
     */
    public function toValidation(): string
    {
        $rules = [];

        foreach ($this->form->getFields() as $field) {
            $fieldRules = $field->getRules();
            if (!empty($fieldRules)) {
                $rules[$field->getName()] = $fieldRules;
            }
        }

        return 'const validationRules = ' . json_encode($rules, JSON_PRETTY_PRINT) . ';';
    }

    /**
     * Generate complete script block for the form
     */
    public function toScript(): string
    {
        $xData = $this->toXData();
        $validation = $this->toValidation();

        return <<<JS
<script>
{$validation}

function formForge() {
    return {$xData};
}
</script>
JS;
    }

    /**
     * Generate x-model binding for a field
     */
    public static function xModel(string $fieldName): string
    {
        return 'x-model="form.' . $fieldName . '"';
    }

    /**
     * Generate x-show binding for conditional visibility
     */
    public static function xShow(string $field, string $operator, mixed $value): string
    {
        $valueStr = is_string($value) ? "'{$value}'" : json_encode($value);
        return 'x-show="form.' . $field . ' ' . $operator . ' ' . $valueStr . '"';
    }

    /**
     * Generate error display binding
     */
    public static function xError(string $fieldName): string
    {
        return 'x-show="errors.' . $fieldName . '" x-text="errors.' . $fieldName . '"';
    }
}
