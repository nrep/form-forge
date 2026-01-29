<?php

declare(strict_types=1);

namespace FormForge\Fields;

class RadioField extends Field
{
    protected string $type = 'radio';
    protected array $options = [];
    protected bool $inline = false;
    protected bool $showAsGrid = false;
    protected int $gridCols = 4;

    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;
        return $this;
    }

    public function grid(int $cols = 4): static
    {
        $this->showAsGrid = true;
        $this->gridCols = $cols;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isInline(): bool
    {
        return $this->inline;
    }

    public function isGrid(): bool
    {
        return $this->showAsGrid;
    }

    public function getGridCols(): int
    {
        return $this->gridCols;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
            'inline' => $this->inline,
            'showAsGrid' => $this->showAsGrid,
            'gridCols' => $this->gridCols,
        ]);
    }
}
