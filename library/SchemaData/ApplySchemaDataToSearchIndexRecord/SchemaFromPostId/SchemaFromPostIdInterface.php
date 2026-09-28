<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId;

use Municipio\Schema\BaseType;

interface SchemaFromPostIdInterface
{
    public function getSchema(int $postId): BaseType;
}
