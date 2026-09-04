<?php

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostMeta;

class ApplySchemaDataToSearchIndexRecord implements Hookable {
    public function __construct(private AddFilter&GetPostMeta $wpService)
    {
    }    

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/Record', [$this, 'applySchemaDataToSearchIndexRecord'], 10, 2);
    }

    public function applySchemaDataToSearchIndexRecord(array $record, int $postId): array
    {
        $schemaData = $this->wpService->getPostMeta($postId, 'schemaData', true);
        
        if (!empty($schemaData)) {
            $record['schema_data'] = $schemaData;
        }

        return $record;
    }
}