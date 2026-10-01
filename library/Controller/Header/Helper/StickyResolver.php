<?php

namespace Municipio\Controller\Header\Helper;

use Municipio\Controller\Header\Helper\Enums;

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

        $lowerHeaderRawMenuItems = $this->extractMenuItems->getHeaderItems(Enums::HEADER_KEY::LOWER->value);

        if (!empty($lowerHeaderRawMenuItems[Enums::HEADER_BREAKPOINT::MOBILE->value])) {
            return $headerId === Enums::HEADER_KEY::LOWER->value ? true : false;
        }

        return $headerId === Enums::HEADER_KEY::UPPER->value ? true : false;
    }
}