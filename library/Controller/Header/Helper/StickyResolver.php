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

        $upperHasItems = $this->headerHasItems(Enums::HEADER_KEY::UPPER->value);
        $lowerHasItems = $this->headerHasItems(Enums::HEADER_KEY::LOWER->value);

        if ($upperHasItems && $lowerHasItems) {
            return $headerId === Enums::HEADER_KEY::LOWER->value;
        }

        if ($lowerHasItems) {
            return $headerId === Enums::HEADER_KEY::LOWER->value;
        }

        if ($upperHasItems) {
            return $headerId === Enums::HEADER_KEY::UPPER->value;
        }

        return false;
    }

    private function headerHasItems(string $headerId): bool
    {
        $rawMenuItems = $this->extractMenuItems->getHeaderItems($headerId);

        return !empty($rawMenuItems[Enums::HEADER_BREAKPOINT::DESKTOP->value])
            || !empty($rawMenuItems[Enums::HEADER_BREAKPOINT::MOBILE->value]);
    }
}