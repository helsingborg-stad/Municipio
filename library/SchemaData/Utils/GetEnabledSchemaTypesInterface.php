<?php

namespace Municipio\SchemaData\Utils;

interface GetEnabledSchemaTypesInterface
{
    /**
     * Get the enabled schema types and properties.
     *
     * @return array
     */
    public function getEnabledSchemaTypesAndProperties(): array;
}
