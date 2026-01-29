<?php

declare(strict_types=1);

namespace FormForge\Fields;

class DateField extends Field
{
    protected string $type = 'date';
    protected ?string $format = 'Y-m-d';
    protected ?string $minDate = null;
    protected ?string $maxDate = null;

    public function format(string $format): static
    {
        $this->format = $format;
        $this->attrs['data-format'] = $format;
        return $this;
    }

    public function minDate(string $date): static
    {
        $this->minDate = $date;
        $this->attrs['min'] = $date;
        return $this;
    }

    public function maxDate(string $date): static
    {
        $this->maxDate = $date;
        $this->attrs['max'] = $date;
        return $this;
    }

    public function today(): static
    {
        $this->default = date('Y-m-d');
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
        ]);
    }
}
