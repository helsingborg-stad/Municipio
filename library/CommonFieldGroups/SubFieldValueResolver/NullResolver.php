<?php

namespace Municipio\CommonFieldGroups\SubFieldValueResolver;

/**
 * Class NullResolver
 *
 * Resolves to null.
 */
class NullResolver implements SubFieldValueResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(array $subField, string $subFieldKey): mixed
    {
        return null;
    }
}
