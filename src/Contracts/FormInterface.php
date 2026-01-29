<?php

declare(strict_types=1);

namespace FormForge\Contracts;

/**
 * Interface for form components
 */
interface FormInterface
{
    public static function make(): static;
    public static function fromSchema(string $schemaClass): static;
    public function schema(array $fields): static;
    public function field(string $name): ?object;
    public function getFields(): array;
    public function action(string $action): static;
    public function method(string $method): static;
    public function values(array $values): static;
    public function errors(array $errors): static;
    public function renderer(RendererInterface $renderer): static;
    public function render(): string;
}
