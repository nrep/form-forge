# FormForge

Schema-driven form builder for PHP. Framework agnostic with Tailwind CSS, Bootstrap, and Alpine.js support.

## Installation

```bash
composer require nrep/form-forge
```

## Quick Start

```php
<?php

use FormForge\Fields\Field;
use FormForge\Form;
use FormForge\Renderers\TailwindRenderer;

// Create fields using fluent API
$form = Form::make()
    ->action('/submit')
    ->method('POST')
    ->schema([
        Field::text('name')->label('Full Name')->required(),
        Field::email('email')->label('Email Address')->required(),
        Field::password('password')->label('Password')->required()->min(8),
        Field::select('country')
            ->label('Country')
            ->options([
                'rw' => 'Rwanda',
                'ke' => 'Kenya',
                'tz' => 'Tanzania',
                'ug' => 'Uganda',
            ]),
        Field::textarea('bio')->label('Bio')->rows(4),
        Field::toggle('newsletter')->label('Subscribe to newsletter'),
    ]);

// Render with Tailwind CSS
$renderer = new TailwindRenderer();
echo $form->render($renderer);
```

## Available Field Types

| Field Type | Method | Description |
|------------|--------|-------------|
| Text | `Field::text()` | Single-line text input |
| Email | `Field::email()` | Email input with validation |
| Password | `Field::password()` | Password input |
| Number | `Field::number()` | Numeric input |
| Money | `Field::money()` | Currency input |
| Select | `Field::select()` | Dropdown select |
| Textarea | `Field::textarea()` | Multi-line text |
| Checkbox | `Field::checkbox()` | Checkbox input |
| Toggle | `Field::toggle()` | Toggle switch |
| Date | `Field::date()` | Date picker |
| DateTime | `Field::dateTime()` | Date and time picker |
| Hidden | `Field::hidden()` | Hidden input |

## Field Configuration

```php
Field::text('username')
    ->label('Username')
    ->placeholder('Enter username')
    ->hint('Must be unique')
    ->required()
    ->disabled()
    ->readonly()
    ->default('guest')
    ->class('custom-class')
    ->attrs(['data-validate' => 'true']);
```

## Validation Rules

```php
Field::text('username')
    ->required()
    ->min(3)
    ->max(20)
    ->regex('/^[a-z0-9_]+$/');

Field::email('email')
    ->required()
    ->email();

Field::number('age')
    ->min(18)
    ->max(100);
```

## Renderers

### Tailwind CSS (Default)

```php
use FormForge\Renderers\TailwindRenderer;

$renderer = new TailwindRenderer([
    'inputClass' => 'input',
    'labelClass' => 'label',
    'errorClass' => 'text-red-500 text-xs',
]);
```

## Alpine.js Integration

```php
use FormForge\JavaScript\AlpineAdapter;

$adapter = new AlpineAdapter();
$field = Field::text('search')
    ->alpine('x-model', 'searchQuery')
    ->alpine('x-on:input', 'handleSearch()');
```

## License

MIT License - see [LICENSE](LICENSE) file.
