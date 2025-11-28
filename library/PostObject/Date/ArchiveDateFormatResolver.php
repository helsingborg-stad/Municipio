<?php

namespace Municipio\PostObject\Date;

use DateTime;
use Municipio\Helper\DateFormat;
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
     * Returns the formatted archive date string for the current post type.
     */
    public function resolve(): string
    {
        $postType          = $this->postObject->getPostType();
        $themeModKey       = sprintf('archive_%s_date_format', $postType);
        $dateFormatSetting = $this->wpService->getThemeMod($themeModKey, 'date-time');

        return DateFormat::getDateFormat($dateFormatSetting);
    }
}
