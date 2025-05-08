<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder;

use Municipio\Schema\BaseType;

interface FormFactoryInterface
{
    public function createForm(BaseType $schemaType): array;
}
