<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;

interface FormFactoryInterface
{
    /**
     * Create a form for the given schema.
     *
     * @param BaseType $schema The schema object.
     *
     * @return array The form fields.
     */
    public function createForm(BaseType $schema): array;
}
