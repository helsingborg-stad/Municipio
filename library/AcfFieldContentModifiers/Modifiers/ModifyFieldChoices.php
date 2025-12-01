<?php

namespace Municipio\AcfFieldContentModifiers\Modifiers;

use Municipio\AcfFieldContentModifiers\AcfFieldContentModifierInterface;

class ModifyFieldChoices implements AcfFieldContentModifierInterface
{
    public function __construct(private array $choices)
    {
    }

    public function modifyFieldContent(array $field): array
    {
        $field['choices'] = $this->choices;
        return $field;
    }
}
