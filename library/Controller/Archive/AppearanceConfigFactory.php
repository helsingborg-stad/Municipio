<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;

/**
 * Factory class for creating AppearanceConfig instances
 */
class AppearanceConfigFactory
{
    /**
     * Create an AppearanceConfig instance
     *
     * @param array $data
     * @return AppearanceConfigInterface
     */
    public function create(array $data): AppearanceConfigInterface
    {
        return (new AppearanceConfigBuilder())
            ->setNumberOfColumns((new Mappers\AppearanceConfigMappers\MapNumberOfColumns())->map($data))
            ->setShouldDisplayFeaturedImage((new Mappers\AppearanceConfigMappers\MapShouldDisplayFeaturedImage())->map($data))
            ->setShouldDisplayReadingTime((new Mappers\AppearanceConfigMappers\MapShouldDisplayReadingTime())->map($data))
            ->setTaxonomiesToDisplay((new Mappers\AppearanceConfigMappers\MapTaxonomiesToDisplay())->map($data))
            ->setPostPropertiesToDisplay((new Mappers\AppearanceConfigMappers\MapPostPropertiesToDisplay())->map($data))
            ->setDesign((new Mappers\AppearanceConfigMappers\MapDesign())->map($data))
            ->build();
    }
}
