<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId;

use Municipio\Schema\BaseType;

interface SchemaFromPostIdInterface
{
    /**
     * Get the schema for a post.
     *
     * @param int $postId
     *
     * @return BaseType
     */
    public function getSchema(int $postId): BaseType;
}
