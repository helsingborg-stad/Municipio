<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

interface FieldWithSubFieldsInterface extends FieldInterface
{
    /**
     * Get the subfields of the field.
     *
     * @return array
     */
    public function getSubFields(): array;
}
