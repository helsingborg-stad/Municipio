<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

interface SubFieldValueResolverInterface
{
    /**
     * Resolves the value of a ACF subfield.
     */
    public function resolve(array $subField, string $subFieldKey): mixed;
}
