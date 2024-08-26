<?php

namespace Municipio\Controller\Header;

class FlipKeyValueTransformer
{
    // Flips the key and value of an array.
    public function transform(array $desktopItems, array $mobileItems): array
    {
        return ['desktop' => array_flip($desktopItems), 'mobile' => array_flip($mobileItems)];
    }
}
