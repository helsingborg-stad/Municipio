<?php

namespace Municipio\AcfFieldContentModifiers;

interface AcfFieldContentModifierRegistrarInterface
{
    /**
     * Register a modifier for a specific field key.
     *
     * @param string $fieldKey
     * @param AcfFieldContentModifierInterface $modifier
     */
    public function registerModifier(string $fieldKey, AcfFieldContentModifierInterface $modifier): void;
}
