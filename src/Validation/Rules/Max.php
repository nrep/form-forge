<?php

declare(strict_types=1);

namespace FormForge\Validation\Rules;

use FormForge\Contracts\RuleInterface;

class Max implements RuleInterface
{
    protected int|float $max;

    public function __construct(int|float $max)
    {
        $this->max = $max;
    }

    public function passes(mixed $value, array $data = []): bool
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return true;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $this->max;
        }

        if (is_array($value)) {
            return count($value) <= $this->max;
        }

        return $value <= $this->max;
    }

    public function message(): string
    {
        return "This field must not exceed {$this->max}.";
    }
}
