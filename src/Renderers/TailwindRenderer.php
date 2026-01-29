<?php

declare(strict_types=1);

namespace FormForge\Renderers;

use FormForge\Contracts\RendererInterface;
use FormForge\Contracts\FieldInterface;
use FormForge\Contracts\FormInterface;
use FormForge\Fields\SelectField;
use FormForge\Fields\TextareaField;
use FormForge\Fields\CheckboxField;
use FormForge\Fields\ToggleField;
use FormForge\Fields\MoneyField;

/**
 * Tailwind CSS renderer for FormForge fields and forms
 */
class TailwindRenderer implements RendererInterface
{
    protected array $config = [
        'inputClass' => 'input w-full',
        'labelClass' => 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1',
        'labelRequiredClass' => '',
        'requiredIndicator' => '<span class="text-red-500 ml-1">*</span>',
        'errorClass' => 'text-red-500 text-xs mt-1',
        'hintClass' => 'text-gray-500 dark:text-gray-400 text-xs mt-1',
        'wrapperClass' => 'mb-4',
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function renderField(FieldInterface $field, array $options = []): string
    {
        $value = $options['value'] ?? $field->getDefault();
        $error = $options['error'] ?? null;
        $data = $field->toArray();

        $html = '<div class="' . $this->getWrapperClass($field) . '">';

        // Label (except for checkbox/toggle/radio which have inline labels, and hidden)
        if ($field->getLabel() && !in_array($field->getType(), ['checkbox', 'toggle', 'hidden'])) {
            $html .= $this->renderLabel($field);
        }

        // Input wrapper (for prefix/suffix)
        if ($field->getType() !== 'hidden') {
            $html .= '<div class="relative">';
        }

        // Render prefix if set
        if (isset($data['attrs']['data-prefix'])) {
            $html .= '<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">'
                . htmlspecialchars($data['attrs']['data-prefix'])
                . '</span>';
        }

        // Render based on type
        $html .= match ($field->getType()) {
            'select' => $this->renderSelect($field, $value),
            'textarea' => $this->renderTextarea($field, $value),
            'checkbox' => $this->renderCheckbox($field, $value),
            'toggle' => $this->renderToggle($field, $value),
            'money' => $this->renderMoney($field, $value),
            'hidden' => $this->renderHidden($field, $value),
            'radio' => $this->renderRadio($field, $value),
            default => $this->renderInput($field, $value),
        };

        // Render suffix if set
        if (isset($data['attrs']['data-suffix'])) {
            $html .= '<span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">'
                . htmlspecialchars($data['attrs']['data-suffix'])
                . '</span>';
        }

        if ($field->getType() !== 'hidden') {
            $html .= '</div>';
        }

        // Error or hint
        if ($error) {
            $html .= '<p class="' . $this->config['errorClass'] . '">' . htmlspecialchars($error) . '</p>';
        } elseif ($data['hint'] ?? null) {
            $html .= '<p class="' . $this->config['hintClass'] . '">' . htmlspecialchars($data['hint']) . '</p>';
        }

        $html .= '</div>';

        return $html;
    }

    protected function renderLabel(FieldInterface $field): string
    {
        $class = $this->config['labelClass'];
        if ($field->isRequired()) {
            $class .= ' ' . $this->config['labelRequiredClass'];
        }

        $required = $field->isRequired() ? $this->config['requiredIndicator'] : '';

        return '<label for="' . htmlspecialchars($field->getName()) . '" class="' . $class . '">'
            . htmlspecialchars($field->getLabel())
            . $required
            . '</label>';
    }

    protected function renderInput(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $inputClass = $this->config['inputClass'];

        // Add padding for prefix/suffix
        if (isset($data['attrs']['data-prefix'])) {
            $inputClass .= ' pl-8';
        }
        if (isset($data['attrs']['data-suffix'])) {
            $inputClass .= ' pr-8';
        }

        $attrs = [
            'type' => $data['type'],
            'name' => $field->getName(),
            'id' => $field->getName(),
            'class' => $inputClass . ' ' . implode(' ', $data['class']),
        ];

        if ($value !== null && $value !== '') {
            $attrs['value'] = htmlspecialchars((string)$value);
        }
        if ($data['placeholder']) {
            $attrs['placeholder'] = $data['placeholder'];
        }
        if ($data['required']) {
            $attrs['required'] = 'required';
        }
        if ($data['disabled']) {
            $attrs['disabled'] = 'disabled';
        }
        if ($data['readonly']) {
            $attrs['readonly'] = 'readonly';
        }

        // Merge additional attrs (excluding data-prefix/suffix)
        foreach ($data['attrs'] as $key => $attrValue) {
            if (!str_starts_with($key, 'data-prefix') && !str_starts_with($key, 'data-suffix')) {
                $attrs[$key] = $attrValue;
            }
        }

        // Add Alpine directives
        foreach ($data['alpine'] as $directive => $expression) {
            $attrs[$directive] = $expression;
        }

        return '<input ' . $this->buildAttrs($attrs) . '>';
    }

    protected function renderSelect(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $attrs = [
            'name' => $field->getName() . ($data['multiple'] ? '[]' : ''),
            'id' => $field->getName(),
            'class' => $this->config['inputClass'] . ' ' . implode(' ', $data['class']),
        ];

        if ($data['required']) {
            $attrs['required'] = 'required';
        }
        if ($data['disabled']) {
            $attrs['disabled'] = 'disabled';
        }
        if ($data['multiple']) {
            $attrs['multiple'] = 'multiple';
        }

        // Alpine directives
        foreach ($data['alpine'] as $directive => $expression) {
            $attrs[$directive] = $expression;
        }

        $html = '<select ' . $this->buildAttrs($attrs) . '>';

        // Empty option
        if ($data['emptyOption'] && !$data['multiple']) {
            $html .= '<option value="">' . htmlspecialchars($data['emptyOption']) . '</option>';
        }

        // Options
        $selectedValues = is_array($value) ? $value : [$value];
        foreach ($data['options'] ?? [] as $optValue => $optLabel) {
            $selected = in_array($optValue, $selectedValues) ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars((string)$optValue) . '"' . $selected . '>'
                . htmlspecialchars($optLabel)
                . '</option>';
        }

        $html .= '</select>';
        return $html;
    }

    protected function renderTextarea(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $attrs = [
            'name' => $field->getName(),
            'id' => $field->getName(),
            'class' => $this->config['inputClass'] . ' ' . implode(' ', $data['class']),
            'rows' => $data['rows'] ?? 3,
        ];

        if ($data['placeholder']) {
            $attrs['placeholder'] = $data['placeholder'];
        }
        if ($data['required']) {
            $attrs['required'] = 'required';
        }
        if ($data['disabled']) {
            $attrs['disabled'] = 'disabled';
        }
        if ($data['readonly']) {
            $attrs['readonly'] = 'readonly';
        }

        foreach ($data['alpine'] as $directive => $expression) {
            $attrs[$directive] = $expression;
        }

        return '<textarea ' . $this->buildAttrs($attrs) . '>'
            . htmlspecialchars((string)($value ?? ''))
            . '</textarea>';
    }

    protected function renderCheckbox(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $checked = $value ? ' checked' : '';

        $html = '<label class="inline-flex items-center cursor-pointer">';
        $html .= '<input type="checkbox" name="' . htmlspecialchars($field->getName()) . '" value="' . htmlspecialchars((string)($data['checkedValue'] ?? '1')) . '"' . $checked;
        $html .= ' class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700"';

        // Alpine directives
        foreach ($data['alpine'] as $directive => $expression) {
            $html .= ' ' . $directive . '="' . htmlspecialchars($expression) . '"';
        }

        $html .= '>';
        if ($data['label']) {
            $html .= '<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">'
                . htmlspecialchars($data['label'])
                . '</span>';
        }
        $html .= '</label>';

        return $html;
    }

    protected function renderToggle(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $checked = $value ? ' checked' : '';

        $html = '<label class="relative inline-flex items-center cursor-pointer">';
        $html .= '<input type="checkbox" name="' . htmlspecialchars($field->getName()) . '" value="1"' . $checked;
        $html .= ' class="sr-only peer"';

        // Alpine directives
        foreach ($data['alpine'] as $directive => $expression) {
            $html .= ' ' . $directive . '="' . htmlspecialchars($expression) . '"';
        }

        $html .= '>';
        $html .= '<div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 ';
        $html .= 'peer-checked:after:translate-x-full peer-checked:after:border-white ';
        $html .= 'after:content-[\'\'] after:absolute after:top-0.5 after:left-[2px] ';
        $html .= 'after:bg-white after:border-gray-300 after:border after:rounded-full ';
        $html .= 'after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>';

        if ($data['label']) {
            $html .= '<span class="ml-3 text-sm text-gray-700 dark:text-gray-300">'
                . htmlspecialchars($data['label'])
                . '</span>';
        }
        $html .= '</label>';

        return $html;
    }

    protected function renderMoney(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $inputClass = $this->config['inputClass'] . ' pl-8';

        $attrs = [
            'type' => 'number',
            'name' => $field->getName(),
            'id' => $field->getName(),
            'class' => $inputClass . ' ' . implode(' ', $data['class']),
            'step' => $data['attrs']['step'] ?? '0.01',
        ];

        if ($value !== null && $value !== '') {
            $attrs['value'] = htmlspecialchars((string)$value);
        }
        if ($data['placeholder']) {
            $attrs['placeholder'] = $data['placeholder'];
        }
        if ($data['required']) {
            $attrs['required'] = 'required';
        }
        if ($data['disabled']) {
            $attrs['disabled'] = 'disabled';
        }
        if ($data['readonly']) {
            $attrs['readonly'] = 'readonly';
        }

        // Alpine directives
        foreach ($data['alpine'] as $directive => $expression) {
            $attrs[$directive] = $expression;
        }

        // Currency symbol prefix
        $currency = $data['currency'] ?? 'USD';
        $symbol = match ($currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'RWF' => 'FRw',
            'KES' => 'KSh',
            'TZS' => 'TSh',
            'UGX' => 'USh',
            default => $currency,
        };

        $html = '<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 text-sm">'
            . htmlspecialchars($symbol)
            . '</span>';
        $html .= '<input ' . $this->buildAttrs($attrs) . '>';

        return $html;
    }

    protected function renderHidden(FieldInterface $field, mixed $value): string
    {
        return '<input type="hidden" name="' . htmlspecialchars($field->getName()) . '" value="' . htmlspecialchars((string)($value ?? '')) . '">';
    }

    protected function renderRadio(FieldInterface $field, mixed $value): string
    {
        $data = $field->toArray();
        $options = $data['options'] ?? [];
        $isGrid = $data['showAsGrid'] ?? false;
        $gridCols = $data['gridCols'] ?? 4;
        $isInline = $data['inline'] ?? false;

        $containerClass = $isGrid 
            ? 'grid grid-cols-' . $gridCols . ' gap-2'
            : ($isInline ? 'flex flex-wrap gap-4' : 'space-y-2');

        $html = '<div class="' . $containerClass . '">';

        foreach ($options as $optValue => $optLabel) {
            $checked = (string)$value === (string)$optValue ? ' checked' : '';
            $id = $field->getName() . '_' . $optValue;

            $html .= '<label class="inline-flex items-center cursor-pointer">';
            $html .= '<input type="radio" name="' . htmlspecialchars($field->getName()) . '" ';
            $html .= 'id="' . htmlspecialchars($id) . '" ';
            $html .= 'value="' . htmlspecialchars((string)$optValue) . '"' . $checked;
            $html .= ' class="rounded-full border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-700"';

            // Alpine directives
            foreach ($data['alpine'] as $directive => $expression) {
                $html .= ' ' . $directive . '="' . htmlspecialchars($expression) . '"';
            }

            $html .= '>';
            $html .= '<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">'
                . htmlspecialchars($optLabel)
                . '</span>';
            $html .= '</label>';
        }

        $html .= '</div>';
        return $html;
    }

    protected function getWrapperClass(FieldInterface $field): string
    {
        $data = $field->toArray();
        $classes = [$this->config['wrapperClass']];

        if ($field->getType() === 'hidden') {
            return 'hidden';
        }

        $classes = array_merge($classes, $data['wrapperClass']);
        return implode(' ', array_filter($classes));
    }

    protected function buildAttrs(array $attrs): string
    {
        $parts = [];
        foreach ($attrs as $key => $value) {
            if ($value === true) {
                $parts[] = $key;
            } elseif ($value !== false && $value !== null) {
                $parts[] = $key . '="' . htmlspecialchars((string)$value) . '"';
            }
        }
        return implode(' ', $parts);
    }

    public function renderForm(FormInterface $form, array $options = []): string
    {
        $html = '';
        $values = $options['values'] ?? [];
        $errors = $options['errors'] ?? [];

        foreach ($form->getFields() as $field) {
            $html .= $this->renderItem($field, $values, $errors);
        }

        return $html;
    }

    protected function renderItem($item, array $values, array $errors): string
    {
        // Check if it's a layout component
        if (method_exists($item, 'isLayout') && $item->isLayout()) {
            return $this->renderLayout($item, $values, $errors);
        }

        // Regular field
        $name = $item->getName();
        $value = $values[$name] ?? $item->getDefault();
        $error = $errors[$name] ?? null;

        return $this->renderField($item, [
            'value' => $value,
            'error' => $error,
        ]);
    }

    protected function renderLayout($layout, array $values, array $errors): string
    {
        $type = $layout->getType();

        return match ($type) {
            'grid' => $this->renderGrid($layout, $values, $errors),
            'section' => $this->renderSection($layout, $values, $errors),
            default => $this->renderDefaultLayout($layout, $values, $errors),
        };
    }

    protected function renderGrid($grid, array $values, array $errors): string
    {
        $cols = $grid->getColumns();
        $gap = $grid->getGap();

        $html = '<div class="grid grid-cols-1 md:grid-cols-' . $cols . ' gap-' . $gap . '">';

        foreach ($grid->getFields() as $field) {
            $html .= $this->renderItem($field, $values, $errors);
        }

        $html .= '</div>';
        return $html;
    }

    protected function renderSection($section, array $values, array $errors): string
    {
        $html = '<div class="' . ($section->getClass() ?? 'space-y-4') . '">';

        if ($section->getHeading()) {
            $html .= '<h3 class="text-lg font-medium text-gray-900 dark:text-white">'
                . htmlspecialchars($section->getHeading())
                . '</h3>';
        }

        if ($section->getDescription()) {
            $html .= '<p class="text-sm text-gray-500 dark:text-gray-400">'
                . htmlspecialchars($section->getDescription())
                . '</p>';
        }

        foreach ($section->getFields() as $field) {
            $html .= $this->renderItem($field, $values, $errors);
        }

        $html .= '</div>';
        return $html;
    }

    protected function renderDefaultLayout($layout, array $values, array $errors): string
    {
        $html = '<div>';
        foreach ($layout->getFields() as $field) {
            $html .= $this->renderItem($field, $values, $errors);
        }
        $html .= '</div>';
        return $html;
    }
}
