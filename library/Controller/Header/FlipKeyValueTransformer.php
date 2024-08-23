<?php

namespace Municipio\Controller\Header;

class FlipKeyValueTransformer
{
    public function transform(array $desktopItems, array $mobileItems): array
    {
        return ['desktop' => array_flip($desktopItems), 'mobile' => array_flip($mobileItems)];
    }
}
