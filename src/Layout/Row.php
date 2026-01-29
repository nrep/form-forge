<?php

declare(strict_types=1);

namespace FormForge\Layout;

use FormForge\Contracts\LayoutInterface;

/**
 * Row layout component for horizontal field grouping
 */
class Row implements LayoutInterface
{
    protected array $fields = [];
    protected string $gap = 'gap-4';
    protected array $classes = [];
    protected ?int $columns = null;

    public static function make(array $fields = []): static
    {
        $row = new static();
        $row->fields = $fields;
        return $row;
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;
        return $this;
    }

    public function gap(string $gap): static
    {
        $this->gap = $gap;
        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function class(string ...$classes): static
    {
        $this->classes = array_merge($this->classes, $classes);
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function render(): string
    {
        $cols = $this->columns ?? count($this->fields);
        // Use explicit class names for Tailwind JIT compatibility
        $colClass = match ($cols) {
            1 => 'md:grid-cols-1',
            2 => 'md:grid-cols-2',
            3 => 'md:grid-cols-3',
            4 => 'md:grid-cols-4',
            5 => 'md:grid-cols-5',
            6 => 'md:grid-cols-6',
            default => 'md:grid-cols-' . min($cols, 12),
        };
        $classes = array_merge(['grid', 'grid-cols-1', $colClass, $this->gap], $this->classes);
        return '<div class="' . implode(' ', $classes) . '">';
    }

    public function renderClose(): string
    {
        return '</div>';
    }

    public function toArray(): array
    {
        return [
            'type' => 'row',
            'fields' => array_map(fn($f) => $f->toArray(), $this->fields),
            'gap' => $this->gap,
            'columns' => $this->columns,
            'classes' => $this->classes,
        ];
    }
}
