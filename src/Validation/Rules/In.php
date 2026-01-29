<?php

declare(strict_types=1);

namespace FormForge\Validation\Rules;

use FormForge\Contracts\RuleInterface;

class In implements RuleInterface
{
    protected array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function passes(mixed $value, array $data = []): bool
    {
        if (empty($value)) {
            return true;
        }

        return in_array($value, $this->values, true);
    }

    public function message(): string
    {
        return 'The selected value is invalid.';
    }
}
