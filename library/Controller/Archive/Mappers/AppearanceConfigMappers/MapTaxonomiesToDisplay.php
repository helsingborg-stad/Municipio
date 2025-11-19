<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

/**
 * Maps taxonomies to display from the provided data
 */
class MapTaxonomiesToDisplay
{
    /**
     * Maps taxonomies to display
     *
     * @param array $data Archive configuration data
     * @return array List of taxonomies to display
     */
    public function map(array $data): array
    {
        $props               = $data['archiveProps'] ?? (object) [];
        $taxonomiesToDisplay = isset($props->taxonomiesToDisplay) ? $props->taxonomiesToDisplay : [];
        return $this->sanitizeInput($taxonomiesToDisplay);
    }

    /**
     * Sanitize input to ensure it is an array of non-empty strings
     *
     * @param mixed $input Input value to sanitize
     * @return array Sanitized array of strings
     */
    private function sanitizeInput(mixed $input): array
    {
        if (!is_array($input)) {
            $input = [trim((string)$input)];
        }

        return array_values(array_filter($input));
    }
}
