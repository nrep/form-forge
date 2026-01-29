<?php

declare(strict_types=1);

namespace FormForge\Contracts;

/**
 * Interface for layout components
 */
interface LayoutInterface
{
    public function getFields(): array;
    public function render(): string;
    public function renderClose(): string;
    public function toArray(): array;
}
