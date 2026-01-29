<?php

declare(strict_types=1);

namespace FormForge\Contracts;

/**
 * Interface for validation components
 */
interface ValidatorInterface
{
    public static function make(array $data, array $rules, array $messages = []): static;
    public function validate(): bool;
    public function passes(): bool;
    public function fails(): bool;
    public function errors(): array;
    public function validated(): array;
    public function firstError(string $field): ?string;
}
