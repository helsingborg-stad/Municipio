<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

/**
 * Builder for AppearanceConfig
 */
class AppearanceConfigBuilder
{
    private int $numberOfColumns = 1;
    private bool $shouldDisplayFeaturedImage = false;
    private bool $shouldDisplayReadingTime = false;
    private array $taxonomiesToDisplay = [];
    private array $postPropertiesToDisplay = [];
    private PostDesign $design = PostDesign::CARD;
    private string $dateSource = 'post_date';
    private DateFormat $dateFormat = DateFormat::DATE_TIME;

    /**
     * Set number of columns
     */
    public function setNumberOfColumns(int $numberOfColumns): self
    {
        $this->numberOfColumns = $numberOfColumns;
        return $this;
    }

    /**
     * Set should display featured image
     */
    public function setShouldDisplayFeaturedImage(bool $shouldDisplayFeaturedImage): self
    {
        $this->shouldDisplayFeaturedImage = $shouldDisplayFeaturedImage;
        return $this;
    }

    /**
     * Set should display reading time
     */
    public function setShouldDisplayReadingTime(bool $shouldDisplayReadingTime): self
    {
        $this->shouldDisplayReadingTime = $shouldDisplayReadingTime;
        return $this;
    }

    /**
     * Set taxonomies to display
     */
    public function setTaxonomiesToDisplay(array $taxonomiesToDisplay): self
    {
        $this->taxonomiesToDisplay = $taxonomiesToDisplay;
        return $this;
    }

    /**
     * Set post properties to display
     */
    public function setPostPropertiesToDisplay(array $postPropertiesToDisplay): self
    {
        $this->postPropertiesToDisplay = $postPropertiesToDisplay;
        return $this;
    }

    /**
     * Set design
     */
    public function setDesign(PostDesign $design): self
    {
        $this->design = $design;
        return $this;
    }

    public function setDateSource(string $dateSource): self
    {
        $this->dateSource = $dateSource;
        return $this;
    }

    public function setDateFormat(DateFormat $dateFormat): self
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * Build AppearanceConfig
     */
    public function build(): AppearanceConfigInterface
    {
        return new class(
            $this->numberOfColumns,
            $this->shouldDisplayFeaturedImage,
            $this->shouldDisplayReadingTime,
            $this->taxonomiesToDisplay,
            $this->postPropertiesToDisplay,
            $this->design,
            $this->dateSource,
            $this->dateFormat,
        ) extends DefaultAppearanceConfig {
            /**
             * Constructor
             */
            public function __construct(
                private int $numberOfColumns,
                private bool $shouldDisplayFeaturedImage,
                private bool $shouldDisplayReadingTime,
                private array $taxonomiesToDisplay,
                private array $postPropertiesToDisplay,
                private PostDesign $design,
                private string $dateSource,
                private DateFormat $dateFormat,
            ) {}

            /**
             * @inheritDoc
             */
            public function getDesign(): PostDesign
            {
                return $this->design;
            }

            /**
             * @inheritDoc
             */
            public function shouldDisplayFeaturedImage(): bool
            {
                return $this->shouldDisplayFeaturedImage;
            }

            /**
             * @inheritDoc
             */
            public function shouldDisplayReadingTime(): bool
            {
                return $this->shouldDisplayReadingTime;
            }

            /**
             * @inheritDoc
             */
            public function getTaxonomiesToDisplay(): array
            {
                return $this->taxonomiesToDisplay;
            }

            /**
             * @inheritDoc
             */
            public function getPostPropertiesToDisplay(): array
            {
                return $this->postPropertiesToDisplay;
            }

            /**
             * @inheritDoc
             */
            public function getNumberOfColumns(): int
            {
                return $this->numberOfColumns;
            }

            /**
             * @inheritDoc
             */
            public function getDateSource(): string
            {
                return $this->dateSource;
            }

            /**
             * @inheritDoc
             */
            public function getDateFormat(): DateFormat
            {
                return $this->dateFormat;
            }
        };
    }
}
