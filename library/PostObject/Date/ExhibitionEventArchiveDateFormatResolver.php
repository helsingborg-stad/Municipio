<?php

namespace Municipio\PostObject\Date;

use Municipio\Helper\DateFormat;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\_x;
use WpService\Contracts\DateI18n;
use WpService\Contracts\GetThemeMod;

class ExhibitionEventArchiveDateFormatResolver implements ArchiveDateFormatResolverInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private GetThemeMod&_x&DateI18n $wpService,
        private ?ArchiveDateFormatResolverInterface $archiveDateFormatSettingResolver
    ) {
    }

    public function resolve(): string
    {
        $postType              = $this->postObject->getPostType();
        $dateFieldThemeModKey  = "archive_{$postType}_date_field";
        $dateFormatThemeModKey = "archive_{$postType}_date_format";

        $dateField         = $this->wpService->getThemeMod($dateFieldThemeModKey, null);
        $dateFormatSetting = $this->wpService->getThemeMod($dateFormatThemeModKey, 'date');
        $dateFormat        = DateFormat::getDateFormat($dateFormatSetting);

        $schemaType = $this->postObject->getSchemaProperty('@type');
        $startDate  = $this->postObject->getSchemaProperty('startDate');
        $endDate    = $this->postObject->getSchemaProperty('endDate');

        if ($schemaType !== 'ExhibitionEvent' || $dateField !== 'startDate') {
            return $this->archiveDateFormatSettingResolver?->resolve();
        }

        if (empty($startDate)) {
            return $this->archiveDateFormatSettingResolver?->resolve();
        }

        $startDateFormatted = $this->formatDate($startDate, $dateFormat);
        $endDateFormatted   = $this->formatEndDate($endDate, $dateFormat);

        return sprintf('%s - %s', $startDateFormatted, $endDateFormatted);
    }

        /**
         * Format a date object or string.
         */
    private function formatDate($date, string $format): string
    {
        if (empty($date)) {
            return '';
        }
        $formatted = is_a($date, \DateTimeInterface::class)
            ? $this->wpService->dateI18n($format, $date->getTimestamp())
            : (string) $date;

        // Escape each character except spaces
        return preg_replace('/([^\s])/u', '\\\$1', $formatted);
    }

    /**
     * Format the end date or return default text.
     */
    private function formatEndDate($endDate, string $format): string
    {
        if (empty($endDate)) {
            $defaultText = $this->wpService->_x('until further notice', 'ExhibitionEvent', 'municipio');
            return $this->escapeText($defaultText);
        }
        return $this->formatDate($endDate, $format);
    }

    /**
     * Escape each character except spaces.
     */
    private function escapeText(string $text): string
    {
        return preg_replace('/([^\s])/u', '\\\$1', $text);
    }
}
