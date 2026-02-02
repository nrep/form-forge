<?php

declare(strict_types=1);

namespace FormForge\Fields;

use FormForge\Contracts\FieldInterface;
use Closure;

/**
 * Base field class for all form fields
 */
class Field implements FieldInterface
{
    protected string $name;
    protected string $type = 'text';
    protected ?string $label = null;
    protected ?string $placeholder = null;
    protected ?string $hint = null;
    protected mixed $default = null;
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected bool $hidden = false;
    protected array $rules = [];
    protected array $class = [];
    protected array $wrapperClass = [];
    protected array $labelClass = [];
    protected array $style = [];
    protected array $attrs = [];
    protected array $alpine = [];
    protected bool $showInTable = false;
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected ?string $tableWidth = null;
    protected string $tableAlign = 'left';
    protected ?Closure $displayUsing = null;
    protected ?Closure $visible = null;
    protected array $showWhen = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // Factory methods
    public static function make(string $nameOrType, ?string $name = null): static
    {
        // If called with 2 args, first is type, second is name (Field::make('text', 'field_name'))
        // If called with 1 arg from subclass, it's the name (TextField::make('field_name'))
        if ($name !== null) {
            $class = self::resolveFieldClass($nameOrType);
            return new $class($name);
        }
        // Called from subclass with just name
        return new static($nameOrType);
    }

    public static function text(string $name): TextField
    {
        return new TextField($name);
    }

    public static function email(string $name): EmailField
    {
        return new EmailField($name);
    }

    public static function password(string $name): PasswordField
    {
        return new PasswordField($name);
    }

    public static function number(string $name): NumberField
    {
        return new NumberField($name);
    }

    public static function money(string $name): MoneyField
    {
        return new MoneyField($name);
    }

    public static function select(string $name): SelectField
    {
        return new SelectField($name);
    }

    public static function textarea(string $name): TextareaField
    {
        return new TextareaField($name);
    }

    public static function checkbox(string $name): CheckboxField
    {
        return new CheckboxField($name);
    }

    public static function toggle(string $name): ToggleField
    {
        return new ToggleField($name);
    }

    public static function date(string $name): DateField
    {
        return new DateField($name);
    }

    public static function dateTime(string $name): DateTimeField
    {
        return new DateTimeField($name);
    }

    public static function hidden(string $name): HiddenField
    {
        return new HiddenField($name);
    }

    public static function radio(string $name): RadioField
    {
        return new RadioField($name);
    }

    // Labels & Help
    public function label(string|Closure $label): static
    {
        $this->label = $label instanceof Closure ? $label() : $label;
        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function hint(string $hint): static
    {
        $this->hint = $hint;
        return $this;
    }

    public function helperText(string $text): static
    {
        return $this->hint($text);
    }

    // Validation
    public function required(bool $required = true): static
    {
        $this->required = $required;
        if ($required && !in_array('required', $this->rules)) {
            $this->rules[] = 'required';
        } elseif (!$required) {
            $this->rules = array_filter($this->rules, fn($r) => $r !== 'required');
        }
        return $this;
    }

    public function rules(array|string $rules): static
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        $this->rules = array_merge($this->rules, $rules);
        return $this;
    }

    public function min(int|float $value): static
    {
        $this->rules[] = "min:{$value}";
        $this->attrs['min'] = $value;
        return $this;
    }

    public function max(int|float $value): static
    {
        $this->rules[] = "max:{$value}";
        $this->attrs['max'] = $value;
        return $this;
    }

    public function maxLength(int $length): static
    {
        $this->rules[] = "max_length:{$length}";
        $this->attrs['maxlength'] = $length;
        return $this;
    }

    public function minLength(int $length): static
    {
        $this->rules[] = "min_length:{$length}";
        $this->attrs['minlength'] = $length;
        return $this;
    }

    public function unique(string $table, ?string $column = null, ?int $ignoreId = null): static
    {
        $rule = "unique:{$table}";
        if ($column) {
            $rule .= ",{$column}";
        }
        if ($ignoreId) {
            $rule .= ",{$ignoreId}";
        }
        $this->rules[] = $rule;
        return $this;
    }

    // Form Behavior
    public function default(mixed $value): static
    {
        $this->default = $value;
        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function readonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function autofocus(bool $autofocus = true): static
    {
        if ($autofocus) {
            $this->attrs['autofocus'] = true;
        }
        return $this;
    }

    // Styling
    public function class(string|array $classes): static
    {
        $this->class = is_array($classes) ? $classes : [$classes];
        return $this;
    }

    public function wrapperClass(string|array $classes): static
    {
        $this->wrapperClass = is_array($classes) ? $classes : [$classes];
        return $this;
    }

    public function labelClass(string|array $classes): static
    {
        $this->labelClass = is_array($classes) ? $classes : [$classes];
        return $this;
    }

    public function style(string|array $styles): static
    {
        $this->style = is_array($styles) ? $styles : [$styles];
        return $this;
    }

    public function attrs(array $attributes): static
    {
        $this->attrs = array_merge($this->attrs, $attributes);
        return $this;
    }

    public function prefix(string $prefix): static
    {
        $this->attrs['data-prefix'] = $prefix;
        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->attrs['data-suffix'] = $suffix;
        return $this;
    }

    public function columnSpan(int|string $span): static
    {
        $this->wrapperClass[] = is_int($span) ? "col-span-{$span}" : $span;
        return $this;
    }

    // Alpine.js
    public function alpine(array $directives): static
    {
        $this->alpine = array_merge($this->alpine, $directives);
        return $this;
    }

    public function xModel(string $expression): static
    {
        $this->alpine['x-model'] = $expression;
        return $this;
    }

    public function xShow(string $condition): static
    {
        $this->alpine['x-show'] = $condition;
        return $this;
    }

    public function xOn(string $event, string $handler): static
    {
        $this->alpine["@{$event}"] = $handler;
        return $this;
    }

    // Conditional Display
    public function visible(bool|Closure $condition = true): static
    {
        $this->visible = $condition instanceof Closure ? $condition : fn() => $condition;
        return $this;
    }

    public function showWhen(string $field, string $operator, mixed $value): static
    {
        $this->showWhen = compact('field', 'operator', 'value');
        $this->alpine['x-show'] = "{$field} {$operator} " . json_encode($value);
        return $this;
    }

    public function hideWhen(string $field, string $operator, mixed $value): static
    {
        $this->showWhen = compact('field', 'operator', 'value');
        $this->alpine['x-show'] = "!({$field} {$operator} " . json_encode($value) . ")";
        return $this;
    }

    // Table Integration
    public function showInTable(bool $show = true): static
    {
        $this->showInTable = $show;
        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;
        return $this;
    }

    public function tableWidth(string $width): static
    {
        $this->tableWidth = $width;
        return $this;
    }

    public function tableAlign(string $align): static
    {
        $this->tableAlign = $align;
        return $this;
    }

    public function displayUsing(Closure $callback): static
    {
        $this->displayUsing = $callback;
        return $this;
    }

    // Getters
    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function getHint(): ?string
    {
        return $this->hint;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function value(mixed $value): static
    {
        $this->default = $value;
        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;
        return $this;
    }

    public function getClass(): array
    {
        return $this->class;
    }

    public function getWrapperClass(): array
    {
        return $this->wrapperClass;
    }

    public function getAttrs(): array
    {
        return $this->attrs;
    }

    public function getAlpine(): array
    {
        return $this->alpine;
    }

    public function isVisibleInTable(): bool
    {
        return $this->showInTable;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function getTableWidth(): ?string
    {
        return $this->tableWidth;
    }

    public function getTableAlign(): string
    {
        return $this->tableAlign;
    }

    public function getDisplayUsing(): ?Closure
    {
        return $this->displayUsing;
    }

    // Serialization
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'hint' => $this->hint,
            'default' => $this->default,
            'required' => $this->required,
            'disabled' => $this->disabled,
            'readonly' => $this->readonly,
            'rules' => $this->rules,
            'class' => $this->class,
            'wrapperClass' => $this->wrapperClass,
            'labelClass' => $this->labelClass,
            'attrs' => $this->attrs,
            'alpine' => $this->alpine,
            'showInTable' => $this->showInTable,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'tableWidth' => $this->tableWidth,
            'tableAlign' => $this->tableAlign,
            'showWhen' => $this->showWhen,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    protected static function resolveFieldClass(string $type): string
    {
        $map = [
            'text' => TextField::class,
            'email' => EmailField::class,
            'password' => PasswordField::class,
            'number' => NumberField::class,
            'money' => MoneyField::class,
            'select' => SelectField::class,
            'textarea' => TextareaField::class,
            'checkbox' => CheckboxField::class,
            'toggle' => ToggleField::class,
            'date' => DateField::class,
            'datetime' => DateTimeField::class,
            'hidden' => HiddenField::class,
            'radio' => RadioField::class,
        ];

        return $map[$type] ?? TextField::class;
    }
}
