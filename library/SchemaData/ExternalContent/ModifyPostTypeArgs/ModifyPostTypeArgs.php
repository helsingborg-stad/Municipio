<?php

namespace Municipio\SchemaData\ExternalContent\ModifyPostTypeArgs;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Abstract class for modifying post type arguments.
 */
abstract class ModifyPostTypeArgs implements Hookable, ModifyPostTypeArgsInterface
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service for adding filters.
     */
    public function __construct(private AddFilter $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('register_post_type_args', [$this, 'modify'], 10, 2);
    }

    /**
     * @inheritDoc
     */
    abstract public function modify(array $args, string $postType): array;
}
