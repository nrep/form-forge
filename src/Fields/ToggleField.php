<?php

declare(strict_types=1);

namespace FormForge\Fields;

class ToggleField extends Field
{
    protected string $type = 'toggle';
    protected mixed $onValue = true;
    protected mixed $offValue = false;
    protected ?string $onLabel = null;
    protected ?string $offLabel = null;

    public function onValue(mixed $value): static
    {
        $this->onValue = $value;
        return $this;
    }

    public function offValue(mixed $value): static
    {
        $this->offValue = $value;
        return $this;
    }

    public function onLabel(string $label): static
    {
        $this->onLabel = $label;
        return $this;
    }

    public function offLabel(string $label): static
    {
        $this->offLabel = $label;
        return $this;
    }

    public function getOnValue(): mixed
    {
        return $this->onValue;
    }

    public function getOffValue(): mixed
    {
        return $this->offValue;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'onValue' => $this->onValue,
            'offValue' => $this->offValue,
            'onLabel' => $this->onLabel,
            'offLabel' => $this->offLabel,
        ]);
    }
}
