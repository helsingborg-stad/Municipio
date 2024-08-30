<?php

namespace Municipio\AcfFieldContentModifiers;

interface AcfFieldContentModifierInterface
{
    /**
     * Modify the content of a field.
     *
     * @param array $field The field to be modified.
     * @return array The modified field.
     */
    public function modifyFieldContent(array $field): array;
}
