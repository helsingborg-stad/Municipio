<?php

namespace Municipio\Config\Features\SchemaData;

use Municipio\Config\Contracts\GetEnabledPostTypes;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;

interface SchemaDataConfigInterface extends
    GetEnabledPostTypes,
    TryGetSchemaTypeFromPostType
{
}
