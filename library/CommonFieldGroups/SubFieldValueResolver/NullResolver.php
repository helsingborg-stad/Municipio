<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

class NullResolver implements SubFieldValueResolverInterface
{
    public function resolve(array $subField, string $subFieldKey): mixed
    {
        return null;
    }
}
