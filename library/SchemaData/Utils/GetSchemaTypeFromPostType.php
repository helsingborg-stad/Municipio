<?php

namespace Municipio\SchemaData\Utils;

use AcfService\Contracts\GetField;

class GetSchemaTypeFromPostType implements GetSchemaTypeFromPostTypeInterface
{
    public function __construct(private GetField $acfService)
    {
    }

    /**
     * @inheritDoc
     */
    public function getSchemaTypeFromPostType(string $postType): ?string
    {
        return $this->acfService->getField('schema', $postType . '_options') ?: null;
    }
}
