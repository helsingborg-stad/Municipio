<?php

namespace Municipio\AcfFieldContentModifiers;

interface AcfFieldContentModifierRegistrarInterface
{
    public function registerModifier(string $fieldKey, AcfFieldContentModifierInterface $modifier): void;
}
