<?php

declare(strict_types=1);

namespace FormForge\Contracts;

/**
 * Interface for form field components
 */
interface FieldInterface
{
    public function getName(): string;
    public function getType(): string;
    public function getLabel(): ?string;
    public function isRequired(): bool;
    public function getRules(): array;
    public function getDefault(): mixed;
    public function isVisibleInTable(): bool;
    public function toArray(): array;
    public function toJson(): string;
}
