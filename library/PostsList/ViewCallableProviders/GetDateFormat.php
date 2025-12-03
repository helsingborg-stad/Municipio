<?php

namespace Municipio\PostsList\ViewCallableProviders;

use Municipio\Helper\DateFormat as HelperDateFormat;
use Municipio\PostsList\Config\AppearanceConfig\DateFormat;

/*
 * View utility to get date format
 */
class GetDateFormat implements ViewCallableProviderInterface
{
    /**
     * Constructor
     *
     * @param DateFormat $dateFormat
     */
    public function __construct(
        private DateFormat $dateFormat,
    ) {}

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->getFormat();
    }

    private function getFormat(): string
    {
        static $cachedFormats = [];

        if (isset($cachedFormats[$this->dateFormat->value])) {
            return $cachedFormats[$this->dateFormat->value];
        }

        $format = match ($this->dateFormat) {
            DateFormat::DATE_TIME => HelperDateFormat::getDateFormat('date-time'),
            DateFormat::DATE => HelperDateFormat::getDateFormat('date'),
            DateFormat::TIME => HelperDateFormat::getDateFormat('time'),
            DateFormat::DATE_BADGE => 'date-badge',
        };

        return $cachedFormats[$this->dateFormat->value] = $format;
    }
}
