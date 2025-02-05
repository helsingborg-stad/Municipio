<?php

namespace Municipio\PostObject\Date;

use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetThemeMod;

class ArchiveDateFormatResolver implements ArchiveDateFormatResolverInterface
{
    public function __construct(
        private PostObjectInterface $postObject,
        private GetThemeMod $wpService
    ) {
    }

    public function resolve(): string
    {
        $dateFormat = $this->wpService->getThemeMod('archive_' . $this->postObject->getPostType() . '_date_format', 'date-time');

        return $dateFormat;
    }
}