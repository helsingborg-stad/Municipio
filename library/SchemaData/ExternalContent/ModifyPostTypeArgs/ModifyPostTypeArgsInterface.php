<?php

namespace Municipio\SchemaData\ExternalContent\ModifyPostTypeArgs;

interface ModifyPostTypeArgsInterface
{
    /**
     * Modify the post type arguments.
     */
    public function modify(array $args, string $postType): array;
}
