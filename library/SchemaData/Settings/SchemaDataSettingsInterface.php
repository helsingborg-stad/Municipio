<?php

namespace Municipio\SchemaData;

interface SchemaDataSettingsInterface
{
    public function getPostTypesWithEnabledSchemaType(): array;
    public function getSchemaTypeByPostType(string $postType): string;
}
