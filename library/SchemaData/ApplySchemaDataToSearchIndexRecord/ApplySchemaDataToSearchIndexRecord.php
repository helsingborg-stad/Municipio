<?php

declare(strict_types=1);


namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord;

use Municipio\HooksRegistrar\Hookable;
use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPost;

class ApplySchemaDataToSearchIndexRecord implements Hookable
{
    public function __construct(
        private AddFilter&GetPost $wpService,
        private PostObjectFromWpPostFactoryInterface $postObjectFactory,
        private AllowedSchemaTypes $allowedSchemaTypesService = new AllowedSchemaTypes(),
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/SearchIndex/Record', [$this, 'apply'], 10, 2);
    }

    public function apply(array $record, int $postId): array
    {
        $wpPost = $this->wpService->getPost($postId);
        $post = $this->postObjectFactory->create($wpPost);
        $schema = $post->getSchema();

        if (!$this->allowedSchemaTypesService->isAllowed($schema->getType())) {
            return $record;
        }

        $record['schema' . $schema->getType()] = $schema->toArray();

        return $record;
    }
}
