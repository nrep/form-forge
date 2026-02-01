<?php

declare(strict_types=1);

namespace FormForge\Layout;

use FormForge\Contracts\FieldInterface;

/**
 * Grid layout component for arranging form fields in columns
 */
class Grid
{
    protected array $fields = [];
    protected int $columns = 2;
    protected string $gap = '6';

    public static function make(): static
    {
        return new static();
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function gap(string $gap): static
    {
        $this->gap = $gap;
        return $this;
    }

    public function schema(array $fields): static
    {
        $this->fields = $fields;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getName(): ?string
    {
        return null;
    }

    public function getDefault(): mixed
    {
        return null;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getGap(): string
    {
        return $this->gap;
    }

    public function isLayout(): bool
    {
        return true;
    }

    public function getType(): string
    {
        return 'grid';
    }

    public function toArray(): array
    {
        return [
            'type' => 'grid',
            'columns' => $this->columns,
            'gap' => $this->gap,
            'fields' => array_map(fn($f) => $f->toArray(), $this->fields),
        ];
    }
}
