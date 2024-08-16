<?php

namespace Municipio\SchemaData\Utils;

class GetEnabledSchemaTypes implements GetEnabledSchemaTypesInterface
{
    public function getEnabledSchemaTypesAndProperties(): array
    {
        return array(
            'Place'  => array('geo', 'telephone', 'url'),
            'School' => array()
        );
    }
}
