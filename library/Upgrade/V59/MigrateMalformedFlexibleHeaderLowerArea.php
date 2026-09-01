<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V59;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Clears the known malformed flexible desktop lower header area.
 */
class MigrateMalformedFlexibleHeaderLowerArea
{
    private const HEADER_APPEARANCE_SETTING = 'header_apperance';
    private const LOWER_SECTION_SETTING = 'header_sortable_section_main_lower';
    private const HIDDEN_STORAGE_SETTING = 'header_sortable_hidden_storage';

    /** @var array<int, string> */
    private const MALFORMED_ITEMS = [
        'header-search-form',
        'search-modal',
        'collapsible-search',
        'logotype',
        'brand-text',
        'user',
        'userGroupUrl',
        'primary',
        'language',
        'tab',
        'drawer',
        'mega-menu',
        'siteselector',
    ];

    /**
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * Clear the lower area only when both its order and hidden data match the malformed picker signature.
     */
    public function migrate(): void
    {
        if ($this->wpService->getThemeMod(self::HEADER_APPEARANCE_SETTING, '') !== 'flexible') {
            return;
        }

        $lowerItems = $this->normalizeItems($this->wpService->getThemeMod(self::LOWER_SECTION_SETTING, []));
        $hiddenStorage = $this->getHiddenStorage();
        $hiddenItems = array_keys($hiddenStorage[self::LOWER_SECTION_SETTING] ?? []);

        if ($lowerItems !== self::MALFORMED_ITEMS || $hiddenItems !== self::MALFORMED_ITEMS) {
            return;
        }

        $hiddenStorage[self::LOWER_SECTION_SETTING] = [];

        $this->wpService->setThemeMod(self::LOWER_SECTION_SETTING, []);
        $this->wpService->setThemeMod(self::HIDDEN_STORAGE_SETTING, json_encode($hiddenStorage) ?: '{}');
    }

    /**
     * @return array<int, string>
     */
    private function normalizeItems(mixed $items): array
    {
        if (is_array($items)) {
            return array_values(array_filter($items, static fn(mixed $item): bool => is_string($item) && $item !== ''));
        }

        if (!is_string($items) || trim($items) === '') {
            return [];
        }

        $decoded = json_decode($items, true);

        return is_array($decoded) ? $this->normalizeItems($decoded) : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function getHiddenStorage(): array
    {
        $storage = $this->wpService->getThemeMod(self::HIDDEN_STORAGE_SETTING, []);

        if (is_array($storage)) {
            return $storage;
        }

        if (!is_string($storage) || trim($storage) === '') {
            return [];
        }

        $decoded = json_decode($storage, true);

        return is_array($decoded) ? $decoded : [];
    }
}