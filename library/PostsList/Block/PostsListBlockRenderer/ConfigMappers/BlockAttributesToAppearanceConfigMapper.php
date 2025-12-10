<?php

declare(strict_types=1);

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;

class BlockAttributesToAppearanceConfigMapper
{
    public function map(array $attributes): AppearanceConfigInterface
    {
        return new class($attributes) extends DefaultAppearanceConfig {
            public function __construct(
                private array $attributes,
            ) {}

            public function getNumberOfColumns(): int
            {
                return (int) ($this->attributes['numberOfColumns'] ?? 3);
            }

            public function getDesign(): PostDesign
            {
                return PostDesign::from($this->attributes['design'] ?? 'card');
            }

            public function getDateFormat(): DateFormat
            {
                return DateFormat::from($this->attributes['dateFormat']);
            }

            public function getDateSource(): string
            {
                return $this->attributes['dateSource'];
            }
        };
    }
}
