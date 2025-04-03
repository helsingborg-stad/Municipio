<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class EmptyField
 */
class EmptyField implements FormFieldResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        return [];
    }
}
