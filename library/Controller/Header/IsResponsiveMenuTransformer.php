<?php

namespace Municipio\Controller\Header;

class IsResponsiveMenuTransformer
{
    public function __construct()
    {
    }

    public function transform(array $items, bool $isResponsive): array
    {
        if (!$isResponsive) {
            $items['mobile'] = $items['desktop'];
        }

        return $items;
    }
}
