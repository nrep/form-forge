<?php

declare(strict_types=1);

namespace FormForge\Examples;

use FormForge\Schema;
use FormForge\Fields\Field;

/**
 * Example schema for Product entity
 * 
 * This demonstrates how to define a reusable form/table schema.
 */
class ProductSchema extends Schema
{
    public static function fields(): array
    {
        return [
            Field::text('name')
                ->label(__('products.name'))
                ->required()
                ->maxLength(255)
                ->placeholder('Enter product name')
                ->showInTable()
                ->sortable()
                ->searchable(),

            Field::text('sku')
                ->label(__('products.sku'))
                ->maxLength(50)
                ->placeholder('SKU-001')
                ->showInTable()
                ->searchable(),

            Field::textarea('description')
                ->label(__('products.description'))
                ->rows(3)
                ->placeholder('Product description'),

            Field::select('category_id')
                ->label(__('products.category'))
                ->emptyOption('-- Select Category --')
                ->showInTable(),

            Field::money('cost_price')
                ->label(__('products.cost_price'))
                ->currency('RWF')
                ->min(0)
                ->showInTable()
                ->tableAlign('right'),

            Field::money('selling_price')
                ->label(__('products.selling_price'))
                ->currency('RWF')
                ->required()
                ->min(0)
                ->showInTable()
                ->tableAlign('right'),

            Field::number('min_stock_level')
                ->label(__('products.min_stock'))
                ->min(0)
                ->default(0)
                ->hint('Alert when stock falls below this level'),

            Field::select('unit_id')
                ->label(__('products.unit'))
                ->emptyOption('-- Select Unit --'),

            Field::toggle('is_active')
                ->label(__('products.active'))
                ->default(true)
                ->showInTable(),

            Field::hidden('organization_id'),
        ];
    }
}
