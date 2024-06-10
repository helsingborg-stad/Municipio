<?php

namespace Municipio\SchemaData\SchemaPropertiesForm;

interface GetAcfFieldGroupBySchemaTypeInterface
{
    /**
     * Get the ACF field group by schema type.
     *
     * @param string $schemaType The schema type.
     * @return array The ACF field group.
     */
    public function getAcfFieldGroup(string $schemaType): array;
}
