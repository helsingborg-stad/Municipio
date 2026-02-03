<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use Municipio\Controller\Archive\ArchiveDefaults;
use Municipio\Controller\Archive\ArchivePropertyResolver;

/**
 * Maps whether to display reading time from the provided data
 */
class MapShouldDisplayReadingTime
{
    /**
     * Maps whether to display reading time
     * Supports both camelCase (readingTime) and snake_case (reading_time)
     * Defaults to ArchiveDefaults::DISPLAY_READING_TIME
     *
     * @param array $data Archive configuration data
     * @return bool True if reading time should be displayed, false otherwise
     */
    public function map(array $data): bool
    {
        $args = $data['archiveProps'] ?? (object) [];
        if (!is_object($args)) {
            $args = (object) [];
        }
        return ArchivePropertyResolver::resolveBool(
            $args,
            'readingTime',
            'reading_time'
        );
    }
}
