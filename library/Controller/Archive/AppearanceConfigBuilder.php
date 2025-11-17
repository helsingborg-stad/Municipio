<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

class AppearanceConfigBuilder
{
    private int $numberOfColumns             = 1;
    private bool $shouldDisplayFeaturedImage = false;
    private bool $shouldDisplayReadingTime   = false;
    private array $taxonomiesToDisplay       = [];
    private array $postPropertiesToDisplay   = [];
    private PostDesign $design               = PostDesign::CARD;

    public function setNumberOfColumns(int $numberOfColumns): self
    {
        $this->numberOfColumns = $numberOfColumns;
        return $this;
    }

    public function setShouldDisplayFeaturedImage(bool $shouldDisplayFeaturedImage): self
    {
        $this->shouldDisplayFeaturedImage = $shouldDisplayFeaturedImage;
        return $this;
    }

    public function setShouldDisplayReadingTime(bool $shouldDisplayReadingTime): self
    {
        $this->shouldDisplayReadingTime = $shouldDisplayReadingTime;
        return $this;
    }

    public function setTaxonomiesToDisplay(array $taxonomiesToDisplay): self
    {
        $this->taxonomiesToDisplay = $taxonomiesToDisplay;
        return $this;
    }

    public function setPostPropertiesToDisplay(array $postPropertiesToDisplay): self
    {
        $this->postPropertiesToDisplay = $postPropertiesToDisplay;
        return $this;
    }

    public function setDesign(PostDesign $design): self
    {
        $this->design = $design;
        return $this;
    }

    public function build(): AppearanceConfigInterface
    {
        return new class (
            $this->numberOfColumns,
            $this->shouldDisplayFeaturedImage,
            $this->shouldDisplayReadingTime,
            $this->taxonomiesToDisplay,
            $this->postPropertiesToDisplay,
            $this->design
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
                private PostDesign $design
            ) {
            }

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
        };
    }
}
