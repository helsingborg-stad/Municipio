<?php

declare(strict_types=1);

namespace Municipio\PostObject\Decorators;

use AllowDynamicProperties;
use ComponentLibrary\Integrations\Image\ImageInterface;
use Municipio\PostObject\PostObjectInterface;

/**
 * Controls whether a post object's featured image is available to a view.
 */
#[AllowDynamicProperties]
class PostObjectWithImageVisibility extends AbstractPostObjectDecorator
{
    public function __construct(
        PostObjectInterface $postObject,
        public readonly bool $shouldDisplayImage,
    ) {
        parent::__construct($postObject);
    }

    /**
     * @inheritDoc
     */
    public function getImage(?int $width = null, ?int $height = null): ?ImageInterface
    {
        if (!$this->shouldDisplayImage) {
            return null;
        }

        return parent::getImage($width, $height);
    }
}
