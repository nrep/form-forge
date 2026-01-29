<?php

declare(strict_types=1);

namespace FormForge\Validation\Rules;

use FormForge\Contracts\RuleInterface;

class Regex implements RuleInterface
{
    protected string $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function passes(mixed $value, array $data = []): bool
    {
        if (empty($value)) {
            return true;
        }

        return preg_match($this->pattern, (string)$value) === 1;
    }

    public function message(): string
    {
        return 'The format is invalid.';
    }
}
