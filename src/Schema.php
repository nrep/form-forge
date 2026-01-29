<?php

declare(strict_types=1);

namespace FormForge;

/**
 * Base schema class for defining form/table schemas
 */
abstract class Schema
{
    /**
     * Define the fields for this schema
     *
     * @return array Array of Field objects
     */
    abstract public static function fields(): array;

    /**
     * Create a form from this schema
     */
    public static function form(): Form
    {
        return Form::fromSchema(static::class);
    }

    /**
     * Get fields that should be visible in tables
     */
    public static function tableFields(): array
    {
        return array_filter(static::fields(), fn($field) => $field->isVisibleInTable());
    }

    /**
     * Get validation rules for all fields
     */
    public static function rules(): array
    {
        $rules = [];
        foreach (static::fields() as $field) {
            $fieldRules = $field->getRules();
            if (!empty($fieldRules)) {
                $rules[$field->getName()] = $fieldRules;
            }
        }
        return $rules;
    }

    /**
     * Get field labels for all fields
     */
    public static function labels(): array
    {
        $labels = [];
        foreach (static::fields() as $field) {
            if ($field->getLabel()) {
                $labels[$field->getName()] = $field->getLabel();
            }
        }
        return $labels;
    }

    /**
     * Get default values for all fields
     */
    public static function defaults(): array
    {
        $defaults = [];
        foreach (static::fields() as $field) {
            $default = $field->getDefault();
            if ($default !== null) {
                $defaults[$field->getName()] = $default;
            }
        }
        return $defaults;
    }
}
