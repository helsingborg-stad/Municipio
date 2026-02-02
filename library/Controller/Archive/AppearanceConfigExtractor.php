<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;

/**
 * Extracts async configuration data from appearance config.
 *
 * Follows Single Responsibility Principle - only responsible for extracting appearance data.
 */
class AppearanceConfigExtractor implements AsyncConfigExtractorInterface
{
    private AppearanceConfigInterface $appearanceConfig;

    public function __construct(AppearanceConfigInterface $appearanceConfig)
    {
        $this->appearanceConfig = $appearanceConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(): array
    {
        $data = [];

        // Extract date source
        if (method_exists($this->appearanceConfig, 'getDateSource')) {
            $data['dateSource'] = $this->appearanceConfig->getDateSource() ?? 'post_date';
        }

        // Extract date format
        if (method_exists($this->appearanceConfig, 'getDateFormat')) {
            $dateFormat = $this->appearanceConfig->getDateFormat();
            $data['dateFormat'] = $dateFormat && isset($dateFormat->value)
                ? $dateFormat->value
                : 'date-time';
        }

        // Extract number of columns
        if (method_exists($this->appearanceConfig, 'getNumberOfColumns')) {
            $data['numberOfColumns'] = $this->appearanceConfig->getNumberOfColumns() ?? 1;
        }

        return $data;
    }
}
