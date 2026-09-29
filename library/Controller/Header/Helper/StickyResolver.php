<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\HeaderKey;

class StickyResolver
{
    public function __construct(
        private ExtractMenuItems $extractMenuItems,
        private object $customizer
    )
    {
    }

    public function isSticky(string $headerId): bool
    {
        if (empty($this->customizer->headerSticky)) {
            return false;
        }

        $lowerHeaderRawMenuItems = $this->extractMenuItems->getHeaderItems(HeaderKey::LOWER->value);

        if (
            !empty($lowerHeaderRawMenuItems[HeaderBreakpoint::DESKTOP->value]) ||
            !empty($lowerHeaderRawMenuItems[HeaderBreakpoint::MOBILE->value])
        ) {
            return $headerId === HeaderKey::LOWER->value ? true : false;
        }

        return $headerId === HeaderKey::UPPER->value ? true : false;
    }
}