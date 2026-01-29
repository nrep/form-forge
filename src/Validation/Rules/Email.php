<?php

declare(strict_types=1);

namespace FormForge\Validation\Rules;

use FormForge\Contracts\RuleInterface;

class Email implements RuleInterface
{
    public function passes(mixed $value, array $data = []): bool
    {
        if (empty($value)) {
            return true; // Let required rule handle empty values
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(): string
    {
        return 'Please enter a valid email address.';
    }
}
