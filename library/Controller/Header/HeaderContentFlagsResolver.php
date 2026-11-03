<?php

namespace Municipio\Controller\Header;

/**
 * Resolves simple boolean flags for header content composition.
 */
class HeaderContentFlagsResolver
{
    /**
     * Resolve header content flags.
     *
     * @param array<int, string> $desktopOrderedItems
     * @param array<int, string> $mobileOrderedItems
     *
     * @return array{hasSearch: bool, hasSeparateBrandText: bool}
     */
    public function resolve(
        array $desktopOrderedItems,
        array $mobileOrderedItems,
        bool $currentHasSearch = false,
        bool $currentHasSeparateBrandText = false,
    ): array {
        return [
            'hasSearch' => $currentHasSearch
                || in_array('search-modal', $desktopOrderedItems, true)
                || in_array('search-modal', $mobileOrderedItems, true),
            'hasSeparateBrandText' => $currentHasSeparateBrandText
                || in_array('brand-text', $desktopOrderedItems, true)
                || in_array('brand-text', $mobileOrderedItems, true),
        ];
    }
}