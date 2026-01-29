<?php

declare(strict_types=1);

namespace FormForge\Layout;

use FormForge\Contracts\LayoutInterface;

/**
 * Column layout component for vertical field grouping with span control
 */
class Column implements LayoutInterface
{
    protected array $fields = [];
    protected int $span = 1;
    protected array $classes = [];

    public static function make(array $fields = []): static
    {
        $col = new static();
        $col->fields = $fields;
        return $col;
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;
        return $this;
    }

    public function span(int $span): static
    {
        $this->span = $span;
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

    public function getSpan(): int
    {
        return $this->span;
    }

    public function render(): string
    {
        $spanClass = $this->span > 1 ? 'md:col-span-' . $this->span : '';
        $classes = array_merge([$spanClass], $this->classes);
        return '<div class="' . implode(' ', array_filter($classes)) . '">';
    }

    public function renderClose(): string
    {
        return '</div>';
    }

    public function toArray(): array
    {
        return [
            'type' => 'column',
            'fields' => array_map(fn($f) => $f->toArray(), $this->fields),
            'span' => $this->span,
            'classes' => $this->classes,
        ];
    }
}
