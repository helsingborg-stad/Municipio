<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\ExhibitionEvent;

use DateTime;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\_x;
use WpService\Contracts\DateI18n;

class GetOccasionText implements ViewCallableProviderInterface
{
    public function __construct(
        private DateI18n&_x $wpService,
    ) {}

    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): null|string => $this->getOccasionText($post);
    }

    private function getOccasionText(PostObjectInterface $post): null|string
    {
        $schema = $post->getSchema();
        $startDate = $this->toDateTime($schema->getProperty('startDate'));
        if (!$startDate) {
            return null;
        }

        $endDate = $this->toDateTime($schema->getProperty('endDate'));
        $start = $this->formatDate($startDate, 'j M');
        $end = $endDate ? $this->formatDate($endDate, 'j M Y') : $this->getUntilFurtherNoticeText();

        return "$start - $end";
    }

    private function toDateTime(mixed $value): null|DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            try {
                return new DateTime($value);
            } catch (\Exception) {
                // Ignore and return null
            }
        }
        return null;
    }

    private function formatDate(DateTime $date, string $format): string
    {
        return ucfirst($this->wpService->dateI18n($format, $date->getTimestamp()));
    }

    private function getUntilFurtherNoticeText(): string
    {
        return $this->wpService->_x('until further notice', 'ExhibitionEvent', 'municipio');
    }
}
