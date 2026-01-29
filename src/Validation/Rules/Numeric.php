<?php

declare(strict_types=1);

namespace FormForge\Validation\Rules;

use FormForge\Contracts\RuleInterface;

class Numeric implements RuleInterface
{
    public function passes(mixed $value, array $data = []): bool
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return true; // Let required rule handle empty values
        }

        return is_numeric($value);
    }

    public function message(): string
    {
        return 'This field must be a number.';
    }
}
