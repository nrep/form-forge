<?php

declare(strict_types=1);

namespace FormForge\Fields;

class EmailField extends Field
{
    protected string $type = 'email';

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->rules[] = 'email';
    }
}
