<?php

declare(strict_types=1);

namespace FormForge;

use FormForge\Contracts\FormInterface;
use FormForge\Contracts\RendererInterface;
use FormForge\Renderers\TailwindRenderer;

/**
 * Main form class for building and rendering forms
 */
class Form implements FormInterface
{
    protected array $fields = [];
    protected ?RendererInterface $renderer = null;
    protected string $action = '';
    protected string $method = 'POST';
    protected ?object $model = null;
    protected array $values = [];
    protected array $errors = [];
    protected array $attrs = [];

    public static function make(): static
    {
        return new static();
    }

    public static function fromSchema(string $schemaClass): static
    {
        $form = new static();
        $form->fields = $schemaClass::fields();
        return $form;
    }

    public function schema(array $fields): static
    {
        $this->fields = $fields;
        // If model was set before schema, populate values now
        $this->populateFromModel();
        return $this;
    }

    public function action(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function method(string $method): static
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function model(object $model): static
    {
        $this->model = $model;
        $this->populateFromModel();
        return $this;
    }

    protected function populateFromModel(): void
    {
        if ($this->model === null) {
            return;
        }
        // Auto-populate values from model
        foreach ($this->fields as $field) {
            $name = $field->getName();
            if (isset($this->model->$name)) {
                $this->values[$name] = $this->model->$name;
            } elseif (is_array($this->model) && isset($this->model[$name])) {
                $this->values[$name] = $this->model[$name];
            }
        }
    }

    public function values(array $values): static
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    public function errors(array $errors): static
    {
        $this->errors = $errors;
        return $this;
    }

    public function attrs(array $attrs): static
    {
        $this->attrs = array_merge($this->attrs, $attrs);
        return $this;
    }

    public function renderer(RendererInterface $renderer): static
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function getRenderer(): RendererInterface
    {
        return $this->renderer ?? new TailwindRenderer();
    }

    public function render(): string
    {
        $renderer = $this->getRenderer();
        $html = '';

        foreach ($this->fields as $field) {
            // Handle layout components (Section, Grid, Html) differently
            if ($field instanceof \FormForge\Contracts\LayoutInterface) {
                $html .= $renderer->renderLayout($field, $this->values, $this->errors);
                continue;
            }

            $name = $field->getName();
            if ($name === null) {
                continue; // Skip items without names
            }
            
            $value = $this->values[$name] ?? $field->getDefault();
            $error = $this->errors[$name] ?? null;

            $html .= $renderer->renderField($field, [
                'value' => $value,
                'error' => $error,
            ]);
        }

        return $html;
    }

    public function renderWithForm(): string
    {
        $attrsStr = '';
        foreach ($this->attrs as $key => $value) {
            $attrsStr .= ' ' . $key . '="' . htmlspecialchars((string)$value) . '"';
        }

        $html = '<form action="' . htmlspecialchars($this->action) . '" method="' . $this->method . '"' . $attrsStr . '>';
        $html .= $this->render();
        $html .= '</form>';

        return $html;
    }

    public function field(string $name): ?object
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }
        return null;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getTableFields(): array
    {
        return array_filter($this->fields, fn($f) => $f->isVisibleInTable());
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'method' => $this->method,
            'fields' => array_map(fn($f) => $f->toArray(), $this->fields),
            'values' => $this->values,
            'errors' => $this->errors,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
