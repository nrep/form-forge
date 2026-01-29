<?php

declare(strict_types=1);

namespace FormForge\Fields;

class PasswordField extends Field
{
    protected string $type = 'password';
    protected bool $toggleable = false;
    protected bool $confirmable = false;

    public function toggleable(bool $toggleable = true): static
    {
        $this->toggleable = $toggleable;
        return $this;
    }

    public function confirmable(bool $confirmable = true): static
    {
        $this->confirmable = $confirmable;
        return $this;
    }

    public function isToggleable(): bool
    {
        return $this->toggleable;
    }

    public function isConfirmable(): bool
    {
        return $this->confirmable;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'toggleable' => $this->toggleable,
            'confirmable' => $this->confirmable,
        ]);
    }
}
