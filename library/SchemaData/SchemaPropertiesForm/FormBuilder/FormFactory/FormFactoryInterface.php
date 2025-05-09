<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Schema\BaseType;

interface FormFactoryInterface
{
    public function createForm(BaseType $schema): array;
}
