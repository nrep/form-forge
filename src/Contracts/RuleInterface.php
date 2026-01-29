<?php

declare(strict_types=1);

namespace FormForge\Contracts;

/**
 * Interface for validation rules
 */
interface RuleInterface
{
    public function passes(mixed $value, array $data = []): bool;
    public function message(): string;
}
