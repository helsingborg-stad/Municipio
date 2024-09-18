<?php

namespace Municipio\ExternalContent\ModifyPostTypeArgs;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

abstract class ModifyPostTypeArgs implements Hookable, ModifyPostTypeArgsInterface
{
    public function __construct(private AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('register_post_type_args', [$this, 'modify'], 10, 2);
    }

    abstract public function modify(array $args, string $postType): array;
}
