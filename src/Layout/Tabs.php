<?php

declare(strict_types=1);

namespace FormForge\Layout;

use FormForge\Contracts\LayoutInterface;

/**
 * Tabs layout component for tabbed content organization
 */
class Tabs implements LayoutInterface
{
    protected array $tabs = [];
    protected string $defaultTab = '';
    protected array $classes = [];

    public static function make(): static
    {
        return new static();
    }

    public function tab(string $key, string $label, array $fields, ?string $icon = null): static
    {
        $this->tabs[$key] = [
            'label' => $label,
            'icon' => $icon,
            'fields' => $fields,
        ];

        if (empty($this->defaultTab)) {
            $this->defaultTab = $key;
        }

        return $this;
    }

    public function default(string $key): static
    {
        $this->defaultTab = $key;
        return $this;
    }

    public function class(string ...$classes): static
    {
        $this->classes = array_merge($this->classes, $classes);
        return $this;
    }

    public function getFields(): array
    {
        $allFields = [];
        foreach ($this->tabs as $tab) {
            $allFields = array_merge($allFields, $tab['fields']);
        }
        return $allFields;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function render(): string
    {
        $classes = array_merge(['mb-6'], $this->classes);

        $html = '<div class="' . implode(' ', $classes) . '" x-data="{ activeTab: \'' . htmlspecialchars($this->defaultTab) . '\' }">';

        // Tab buttons
        $html .= '<div class="border-b border-gray-200 dark:border-gray-700">';
        $html .= '<nav class="flex space-x-4" aria-label="Tabs">';

        foreach ($this->tabs as $key => $tab) {
            $html .= '<button type="button" @click="activeTab = \'' . htmlspecialchars($key) . '\'"';
            $html .= ' :class="{ \'border-blue-500 text-blue-600 dark:text-blue-400\': activeTab === \'' . htmlspecialchars($key) . '\', \'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300\': activeTab !== \'' . htmlspecialchars($key) . '\' }"';
            $html .= ' class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors">';

            if ($tab['icon']) {
                $html .= '<i class="' . htmlspecialchars($tab['icon']) . ' mr-2"></i>';
            }

            $html .= htmlspecialchars($tab['label']);
            $html .= '</button>';
        }

        $html .= '</nav>';
        $html .= '</div>';

        // Tab panels
        foreach ($this->tabs as $key => $tab) {
            $html .= '<div x-show="activeTab === \'' . htmlspecialchars($key) . '\'" class="pt-4">';
            // Fields will be rendered by the form
            $html .= '<!-- Tab: ' . htmlspecialchars($key) . ' fields -->';
        }

        return $html;
    }

    public function renderClose(): string
    {
        $html = '';
        // Close all tab panels
        foreach ($this->tabs as $key => $tab) {
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function toArray(): array
    {
        $tabsArray = [];
        foreach ($this->tabs as $key => $tab) {
            $tabsArray[$key] = [
                'label' => $tab['label'],
                'icon' => $tab['icon'],
                'fields' => array_map(fn($f) => $f->toArray(), $tab['fields']),
            ];
        }

        return [
            'type' => 'tabs',
            'tabs' => $tabsArray,
            'defaultTab' => $this->defaultTab,
            'classes' => $this->classes,
        ];
    }
}
