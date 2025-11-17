<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

/**
 * Maps whether to display reading time from the provided data
 */
class MapShouldDisplayReadingTime
{
    /**
     * Maps whether to display reading time
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
        return isset($args->readingTime) ? (bool) $args->readingTime : false;
    }
}
