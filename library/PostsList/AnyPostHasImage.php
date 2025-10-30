<?php

namespace Municipio\PostsList;

use Municipio\PostObject\PostObjectInterface;

class AnyPostHasImage
{
    public static function check(PostObjectInterface ...$posts): bool
    {
        foreach ($posts as $post) {
            if ($post->getImage() !== null) {
                return true;
            }
        }

        return false;
    }
}
