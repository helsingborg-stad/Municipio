<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;
use Municipio\PostsList\Config\AppearanceConfig\DateFormat;

/**
 * Mapper for post date format in appearance config
 */
class MapDateFormat implements MapperInterface
{
    /**
     * Map post date format from data
     * @param array $data
     * @return DateFormat
     */
    public function map(array $data): DateFormat
    {
        return match ($data['archiveProps']->dateFormat ?? '') {
            'date' => DateFormat::DATE,
            'date-time' => DateFormat::DATE_TIME,
            'time' => DateFormat::TIME,
            default => DateFormat::DATE_TIME,
        };
    }
}
