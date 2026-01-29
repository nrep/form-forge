<?php

declare(strict_types=1);

namespace FormForge\Fields;

class HiddenField extends Field
{
    protected string $type = 'hidden';

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->showInTable = false;
    }
}
