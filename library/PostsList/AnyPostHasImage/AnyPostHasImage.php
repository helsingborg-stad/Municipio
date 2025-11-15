<?php

namespace Municipio\PostsList\AnyPostHasImage;

use Municipio\PostObject\PostObjectInterface;

/**
 * Service to check if any post has an image
 */
class AnyPostHasImage implements AnyPostHasImageInterface
{
    /**
     * @inheritDoc
     */
    public function check(PostObjectInterface ...$posts): bool
    {
        foreach ($posts as $post) {
            if ($post->getImage() !== null) {
                return true;
            }
        }

        return false;
    }
}
