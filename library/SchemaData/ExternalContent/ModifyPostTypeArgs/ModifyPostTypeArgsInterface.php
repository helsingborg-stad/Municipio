<?php

namespace Municipio\SchemaData\ExternalContent\ModifyPostTypeArgs;

interface ModifyPostTypeArgsInterface
{
    public function modify(array $args, string $postType): array;
}
