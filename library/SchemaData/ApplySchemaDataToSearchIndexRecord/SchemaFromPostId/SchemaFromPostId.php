<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId;

use Municipio\PostObject\Factory\PostObjectFromWpPostFactoryInterface;
use Municipio\Schema\BaseType;
use WpService\Contracts\GetPost;

class SchemaFromPostId implements SchemaFromPostIdInterface
{
    public function __construct(
        private GetPost $wpService,
        private PostObjectFromWpPostFactoryInterface $postObjectFactory,
    ) {}

    public function getSchema(int $postId): BaseType
    {
        $wpPost = $this->wpService->getPost($postId);
        $post = $this->postObjectFactory->create($wpPost);

        return $post->getSchema();
    }
}
