<?php

declare(strict_types=1);

namespace FormForge\Layout;

use FormForge\Contracts\LayoutInterface;

/**
 * Raw HTML layout component
 */
class Html implements LayoutInterface
{
    protected string $content;
    protected array $fields = []; // Required by LayoutInterface but not used

    public static function make(string $content): static
    {
        $component = new static();
        $component->content = $content;
        return $component;
    }

    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getName(): ?string
    {
        return null;
    }

    public function getDefault(): mixed
    {
        return null;
    }

    public function render(): string
    {
        return $this->content;
    }

    public function renderClose(): string
    {
        return '';
    }

    public function toArray(): array
    {
        return [
            'type' => 'html',
            'content' => $this->content,
        ];
    }
}
