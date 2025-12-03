<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetThemeMod;

/**
 * ArchiveDateSourceResolver class.
 */
class ArchiveDateSourceResolver implements ArchiveDateSourceResolverInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private GetThemeMod $wpService
    ) {
    }

    /**
     * Resolve the archive date setting.
     */
    public function resolve(): string
    {
        return $this->wpService->getThemeMod('archive_' . $this->postObject->getPostType() . '_date_field', 'post_date');
    }
}
