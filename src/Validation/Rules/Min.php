<?php

declare(strict_types=1);

namespace FormForge\Validation\Rules;

use FormForge\Contracts\RuleInterface;

class Min implements RuleInterface
{
    protected int|float $min;

    public function __construct(int|float $min)
    {
        $this->min = $min;
    }

    public function passes(mixed $value, array $data = []): bool
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return true;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $this->min;
        }

        if (is_array($value)) {
            return count($value) >= $this->min;
        }

        return $value >= $this->min;
    }

    public function message(): string
    {
        return "This field must be at least {$this->min}.";
    }
}
