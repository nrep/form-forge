<?php

declare(strict_types=1);

namespace FormForge\Contracts;

/**
 * Interface for form/field renderers
 */
interface RendererInterface
{
    public function renderField(FieldInterface $field, array $options = []): string;
    public function renderForm(FormInterface $form, array $options = []): string;
}
