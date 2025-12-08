<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Schema\ExhibitionEvent;

use DateTime;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\_x;
use WpService\Contracts\DateI18n;

/**
 * Class GetOccasionText
 *
 * Provides utility to format and retrieve exhibition event occasion text (date range).
 */
class GetOccasionText implements ViewCallableProviderInterface
{
    public function __construct(
        private DateI18n&_x $wpService,
    ) {}

    /**
     * Get a callable that retrieves the occasion text for an exhibition event post.
     *
     * @return callable Callable that returns the occasion text for a given post.
     */
    public function getCallable(): callable
    {
        return [$this, 'getOccasionText'];
    }

    /**
     * Get the formatted occasion text for an exhibition event
     *
     * @param PostObjectInterface $post The post object containing the exhibition event schema.
     * @return string|null The formatted occasion text (e.g., "15 Jan - 15 Feb 2024") or null if no start date.
     */
    public function getOccasionText(PostObjectInterface $post): ?string
    {
        $schema = $post->getSchema();
        $startDate = $this->toDateTime($schema->getProperty('startDate'));
        if (!$startDate) {
            return null;
        }

        $endDate = $this->toDateTime($schema->getProperty('endDate'));
        $start = $this->formatDate($startDate, 'j M');
        $end = $endDate ? $this->formatDate($endDate, 'j M Y') : $this->getUntilFurtherNoticeText();

        return "{$start} - {$end}";
    }

    /**
     * Convert a mixed value to a DateTime object
     *
     * @param mixed $value The value to convert (DateTime object or string).
     * @return DateTime|null The DateTime object or null if conversion fails.
     */
    /**
     * Converts a value to a DateTime object if possible.
     *
     * @param mixed $value The value to convert.
     * @return DateTime|null The DateTime object or null if conversion fails.
     */
    private function toDateTime(mixed $value): ?DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            try {
                return new DateTime($value);
            } catch (\Exception) {
                return null;
            }
        }
        return null;
    }

    /**
     * Format a DateTime object according to the specified format and localize it.
     *
     * @param DateTime $date   The date to format.
     * @param string   $format The format string (PHP date format).
     * @return string         The formatted and localized date string.
     */
    private function formatDate(DateTime $date, string $format): string
    {
        return ucfirst($this->wpService->dateI18n($format, $date->getTimestamp()));
    }

    /**
     * Get the localized "until further notice" text
     *
     * @return string The translated text.
     */
    private function getUntilFurtherNoticeText(): string
    {
        return $this->wpService->_x('until further notice', 'ExhibitionEvent', 'municipio');
    }
}
