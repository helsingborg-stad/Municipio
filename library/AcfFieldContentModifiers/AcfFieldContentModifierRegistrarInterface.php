<?php

namespace Municipio\AcfFieldContentModifiers;

interface AcfFieldContentModifierRegistrarInterface
{
    public function registerModifier(AcfFieldContentModifierInterface $modifier): void;
}
