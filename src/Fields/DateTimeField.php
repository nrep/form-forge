<?php

declare(strict_types=1);

namespace FormForge\Fields;

class DateTimeField extends Field
{
    protected string $type = 'datetime-local';
    protected ?string $format = 'Y-m-d\TH:i';
    protected ?string $minDateTime = null;
    protected ?string $maxDateTime = null;

    public function format(string $format): static
    {
        $this->format = $format;
        $this->attrs['data-format'] = $format;
        return $this;
    }

    public function minDateTime(string $datetime): static
    {
        $this->minDateTime = $datetime;
        $this->attrs['min'] = $datetime;
        return $this;
    }

    public function maxDateTime(string $datetime): static
    {
        $this->maxDateTime = $datetime;
        $this->attrs['max'] = $datetime;
        return $this;
    }

    public function now(): static
    {
        $this->default = date('Y-m-d\TH:i');
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'minDateTime' => $this->minDateTime,
            'maxDateTime' => $this->maxDateTime,
        ]);
    }
}
