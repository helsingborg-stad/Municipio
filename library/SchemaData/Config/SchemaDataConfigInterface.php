<?php

namespace Municipio\SchemaData\Config;

use Municipio\SchemaData\Config\Contracts\GetEnabledPostTypes;
use Municipio\SchemaData\Config\Contracts\TryGetSchemaTypeFromPostType;

interface SchemaDataConfigInterface extends
    GetEnabledPostTypes,
    TryGetSchemaTypeFromPostType
{
}
