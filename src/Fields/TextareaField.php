<?php

declare(strict_types=1);

namespace FormForge\Fields;

class TextareaField extends Field
{
    protected string $type = 'textarea';
    protected int $rows = 3;
    protected ?int $cols = null;

    public function rows(int $rows): static
    {
        $this->rows = $rows;
        $this->attrs['rows'] = $rows;
        return $this;
    }

    public function cols(int $cols): static
    {
        $this->cols = $cols;
        $this->attrs['cols'] = $cols;
        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'rows' => $this->rows,
            'cols' => $this->cols,
        ]);
    }
}
