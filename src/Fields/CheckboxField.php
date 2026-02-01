<?php

declare(strict_types=1);

namespace FormForge\Fields;

class CheckboxField extends Field
{
    protected string $type = 'checkbox';
    protected mixed $checkedValue = '1';
    protected mixed $uncheckedValue = '0';

    public function checkedValue(mixed $value): static
    {
        $this->checkedValue = $value;
        return $this;
    }

    public function uncheckedValue(mixed $value): static
    {
        $this->uncheckedValue = $value;
        return $this;
    }

    public function checked(bool $checked = true): static
    {
        $this->default = $checked ? $this->checkedValue : $this->uncheckedValue;
        if ($checked) {
            $this->attrs['checked'] = 'checked';
        }
        return $this;
    }

    public function getCheckedValue(): mixed
    {
        return $this->checkedValue;
    }

    public function getUncheckedValue(): mixed
    {
        return $this->uncheckedValue;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'checkedValue' => $this->checkedValue,
            'uncheckedValue' => $this->uncheckedValue,
        ]);
    }
}
