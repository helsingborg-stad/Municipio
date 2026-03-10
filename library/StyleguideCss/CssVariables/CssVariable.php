<?php

namespace Municipio\StyleguideCss\CssVariables;

class CssVariable implements CssVariableInterface
{
    public function __construct(
        private string $name,
        private string $value,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
