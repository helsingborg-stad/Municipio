<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Repairs malformed flexible-header sortable data produced by legacy migrations.
 *
 * Some migrated sites can end up with empty sortable section arrays while
 * hidden storage still contains item metadata. This migration restores section
 * order from hidden storage and ensures each referenced item has valid default
 * align/margin metadata.
 */
class MigrateFlexibleHeaderSortableDataIntegrity
{
    private const HEADER_APPEARANCE_SETTING = 'header_apperance';
    private const HEADER_HIDDEN_STORAGE_SETTING = 'header_sortable_hidden_storage';

    /** @var array<int, string> */
    private const SECTION_SETTINGS = [
        'header_sortable_section_main_upper',
        'header_sortable_section_main_lower',
        'header_sortable_section_main_upper_responsive',
        'header_sortable_section_main_lower_responsive',
    ];

    /** @var array<int, string> */
    private const VALID_ALIGNMENTS = ['left', 'center', 'right'];

    /** @var array<int, string> */
    private const VALID_MARGINS = ['none', 'left', 'right', 'both'];

    /**
     * Constructor.
     *
     * @param GetThemeMod&SetThemeMod $wpService WordPress service.
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    /**
     * Run migration.
     */
    public function migrate(): void
    {
        if (strtolower((string) $this->wpService->getThemeMod(self::HEADER_APPEARANCE_SETTING, '')) !== 'flexible') {
            return;
        }

        $hiddenStorage = $this->getNormalizedHiddenStorage();
        $storageChanged = false;

        foreach (self::SECTION_SETTINGS as $sectionSetting) {
            $items = $this->normalizeItems($this->wpService->getThemeMod($sectionSetting, []));

            if (empty($items)) {
                $recoveredItems = $this->getOrderedItemsFromHiddenStorage($hiddenStorage[$sectionSetting] ?? null);

                if (!empty($recoveredItems)) {
                    $this->wpService->setThemeMod($sectionSetting, $recoveredItems);
                    $items = $recoveredItems;
                }
            }

            if (!isset($hiddenStorage[$sectionSetting]) || !is_array($hiddenStorage[$sectionSetting])) {
                $hiddenStorage[$sectionSetting] = [];
                $storageChanged = true;
            }

            foreach ($items as $item) {
                $currentOptions = $hiddenStorage[$sectionSetting][$item] ?? null;
                $normalizedOptions = $this->normalizeItemOptions($currentOptions, $sectionSetting, $item);

                if ($currentOptions !== $normalizedOptions) {
                    $hiddenStorage[$sectionSetting][$item] = $normalizedOptions;
                    $storageChanged = true;
                }
            }
        }

        if ($storageChanged) {
            $this->wpService->setThemeMod(self::HEADER_HIDDEN_STORAGE_SETTING, json_encode($hiddenStorage) ?: '{}');
        }
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

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static fn(mixed $item): bool => is_string($item) && $item !== ''));
    }

    /**
     * @return array<string, mixed>
     */
    private function getNormalizedHiddenStorage(): array
    {
        $storage = $this->wpService->getThemeMod(self::HEADER_HIDDEN_STORAGE_SETTING, []);

        if (is_array($storage)) {
            return $storage;
        }

        if (!is_string($storage) || trim($storage) === '') {
            return [];
        }

        $decoded = json_decode($storage, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<int, string>
     */
    private function getOrderedItemsFromHiddenStorage(mixed $storage): array
    {
        if (is_object($storage)) {
            $storage = get_object_vars($storage);
        }

        if (!is_array($storage)) {
            return [];
        }

        return array_values(array_filter(
            array_map(static fn(mixed $item): string => (string) $item, array_keys($storage)),
            static fn(string $item): bool => $item !== '',
        ));
    }

    /**
     * @return array{align: string, margin: string}
     */
    private function normalizeItemOptions(mixed $options, string $sectionSetting, string $item): array
    {
        if (is_object($options)) {
            $options = get_object_vars($options);
        }

        $options = is_array($options) ? $options : [];

        $align = $options['align'] ?? $this->getDefaultAlign($sectionSetting, $item);
        if (!is_string($align) || !in_array($align, self::VALID_ALIGNMENTS, true)) {
            $align = $this->getDefaultAlign($sectionSetting, $item);
        }

        $margin = $options['margin'] ?? 'none';
        if (!is_string($margin) || !in_array($margin, self::VALID_MARGINS, true)) {
            $margin = 'none';
        }

        return [
            'align' => $align,
            'margin' => $margin,
        ];
    }

    private function getDefaultAlign(string $sectionSetting, string $item): string
    {
        if ($item === 'logotype' && str_contains($sectionSetting, 'upper')) {
            return 'left';
        }

        return 'right';
    }
}
