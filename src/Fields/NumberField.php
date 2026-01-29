<?php

declare(strict_types=1);

namespace FormForge\Fields;

class NumberField extends Field
{
    protected string $type = 'number';
    protected ?int $step = null;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->rules[] = 'numeric';
    }

    public function step(int|float $step): static
    {
        $this->step = (int)$step;
        $this->attrs['step'] = $step;
        return $this;
    }

    public function integer(): static
    {
        $this->rules[] = 'integer';
        $this->attrs['step'] = 1;
        return $this;
    }

    public function positive(): static
    {
        $this->min(0);
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'step' => $this->step,
        ]);
    }
}
