<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetThemeMod;

/**
 * ArchiveDateFormatResolver class.
 */
class ArchiveDateFormatResolver implements ArchiveDateFormatResolverInterface
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
     * Resolve the archive date format setting.
     */
    public function resolve(): string
    {
        $dateFormat = $this->wpService->getThemeMod('archive_' . $this->postObject->getPostType() . '_date_format', 'date-time');

        return $dateFormat;
    }
}
