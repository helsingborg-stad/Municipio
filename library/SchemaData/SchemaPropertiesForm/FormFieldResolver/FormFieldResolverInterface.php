<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver;

interface FormFieldResolverInterface
{
    /**
     * Resolve ACF form field properties
     *
     * @return array
     */
    public function resolve(): array;
}
