<?php

namespace Municipio\PostsList\AnyPostHasImage;

interface AnyPostHasImageInterface
{
    /**
     * Check if any of the provided posts has an image.
     *
     * @param \Municipio\PostObject\PostObjectInterface ...$posts
     * @return bool
     */
    public function check(\Municipio\PostObject\PostObjectInterface ...$posts): bool;
}
