<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\Schema\Schema;

class AllowedSchemaTypes
{
    public function getAllowedSchemaTypes(): array
    {
        return [
            Schema::elementarySchool()->getType(),
            Schema::event()->getType(),
            Schema::exhibitionEvent()->getType(),
            Schema::jobPosting()->getType(),
            Schema::place()->getType(),
            Schema::preschool()->getType(),
            Schema::project()->getType(),
            Schema::thing()->getType(),
        ];
    }

    public function isAllowed(string $schemaType): bool
    {
        return in_array($schemaType, $this->getAllowedSchemaTypes(), strict: true);
    }
}
