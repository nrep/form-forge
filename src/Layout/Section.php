<?php

declare(strict_types=1);

namespace FormForge\Layout;

use FormForge\Contracts\LayoutInterface;

/**
 * Section layout component with title and optional description
 */
class Section implements LayoutInterface
{
    protected array $fields = [];
    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $icon = null;
    protected bool $collapsible = false;
    protected bool $collapsed = false;
    protected array $classes = [];

    public static function make(string $title = null): static
    {
        $section = new static();
        $section->title = $title;
        return $section;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;
        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;
        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;
        $this->collapsible = true;
        return $this;
    }

    public function class(string ...$classes): static
    {
        $this->classes = array_merge($this->classes, $classes);
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

    public function getType(): string
    {
        return 'section';
    }

    public function getHeading(): ?string
    {
        return $this->title;
    }

    public function getClass(): ?string
    {
        return !empty($this->classes) ? implode(' ', $this->classes) : null;
    }

    public function render(): string
    {
        $classes = array_merge(['bg-white', 'dark:bg-gray-800', 'rounded-lg', 'shadow-sm', 'border', 'border-gray-200', 'dark:border-gray-700', 'p-6', 'mb-6'], $this->classes);

        $html = '<div class="' . implode(' ', $classes) . '"';

        if ($this->collapsible) {
            $html .= ' x-data="{ open: ' . ($this->collapsed ? 'false' : 'true') . ' }"';
        }

        $html .= '>';

        // Header
        if ($this->title) {
            $html .= '<div class="' . ($this->collapsible ? 'cursor-pointer' : '') . '"';
            if ($this->collapsible) {
                $html .= ' @click="open = !open"';
            }
            $html .= '>';
            $html .= '<div class="flex items-center justify-between mb-4">';
            $html .= '<div class="flex items-center gap-2">';

            if ($this->icon) {
                $html .= '<i class="' . htmlspecialchars($this->icon) . ' text-gray-500 dark:text-gray-400"></i>';
            }

            $html .= '<h3 class="text-lg font-semibold text-gray-900 dark:text-white">'
                . htmlspecialchars($this->title)
                . '</h3>';
            $html .= '</div>';

            if ($this->collapsible) {
                $html .= '<i class="fas fa-chevron-down text-gray-400 transition-transform" :class="{ \'rotate-180\': open }"></i>';
            }

            $html .= '</div>';

            if ($this->description) {
                $html .= '<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">'
                    . htmlspecialchars($this->description)
                    . '</p>';
            }

            $html .= '</div>';
        }

        // Content wrapper for collapsible
        if ($this->collapsible) {
            $html .= '<div x-show="open" x-collapse>';
        }

        return $html;
    }

    public function renderClose(): string
    {
        $html = '';
        if ($this->collapsible) {
            $html .= '</div>'; // x-collapse wrapper
        }
        $html .= '</div>'; // section wrapper
        return $html;
    }

    public function toArray(): array
    {
        return [
            'type' => 'section',
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'fields' => array_map(fn($f) => $f->toArray(), $this->fields),
            'classes' => $this->classes,
        ];
    }
}
