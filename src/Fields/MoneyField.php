<?php

declare(strict_types=1);

namespace FormForge\Fields;

class MoneyField extends Field
{
    protected string $type = 'money';
    protected string $currency = 'USD';
    protected int $decimals = 2;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->rules[] = 'numeric';
        $this->attrs['step'] = '0.01';
    }

    public function currency(string $currency): static
    {
        $this->currency = $currency;
        $this->attrs['data-currency'] = $currency;
        return $this;
    }

    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;
        $this->attrs['step'] = '0.' . str_repeat('0', $decimals - 1) . '1';
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'currency' => $this->currency,
            'decimals' => $this->decimals,
        ]);
    }
}
