<?php

declare(strict_types=1);

namespace FormForge\Fields;

use Closure;

class SelectField extends Field
{
    protected string $type = 'select';
    protected array $options = [];
    protected bool $selectSearchable = false;
    protected bool $multiple = false;
    protected ?string $optionsFrom = null;
    protected ?string $emptyOption = '-- Select --';

    public function options(array|Closure $options): static
    {
        $this->options = $options instanceof Closure ? $options() : $options;
        return $this;
    }

    public function selectSearchable(bool $searchable = true): static
    {
        $this->selectSearchable = $searchable;
        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function emptyOption(?string $label): static
    {
        $this->emptyOption = $label;
        return $this;
    }

    public function optionsFrom(string $alpineVar, string $valueKey = 'id', string $labelKey = 'name'): static
    {
        $this->optionsFrom = $alpineVar;
        $this->attrs['data-options-from'] = $alpineVar;
        $this->attrs['data-value-key'] = $valueKey;
        $this->attrs['data-label-key'] = $labelKey;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function isSelectSearchable(): bool
    {
        return $this->selectSearchable;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function getEmptyOption(): ?string
    {
        return $this->emptyOption;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
            'selectSearchable' => $this->selectSearchable,
            'multiple' => $this->multiple,
            'optionsFrom' => $this->optionsFrom,
            'emptyOption' => $this->emptyOption,
        ]);
    }
}
