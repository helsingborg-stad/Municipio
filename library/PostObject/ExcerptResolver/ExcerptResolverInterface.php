<?php

declare(strict_types=1);

namespace Municipio\PostObject\ExcerptResolver;

use Municipio\PostObject\PostObjectInterface;

interface ExcerptResolverInterface
{
    /**
     * Resolve excerpt for post object.
     */
    public function resolveExcerpt(PostObjectInterface $postObject): string;
}
