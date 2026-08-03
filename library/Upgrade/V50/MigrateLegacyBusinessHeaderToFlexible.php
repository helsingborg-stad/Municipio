<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Migrates legacy business header layout to flexible template configuration.
 */
class MigrateLegacyBusinessHeaderToFlexible
{
    private const HEADER_APPEARANCE_SETTING = 'header_apperance';
    private const HEADER_HIDDEN_STORAGE_SETTING = 'header_sortable_hidden_storage';
    private const UPPER_SECTION_SETTING = 'header_sortable_section_main_upper';
    private const LOWER_SECTION_SETTING = 'header_sortable_section_main_lower';
    private const UPPER_RESPONSIVE_SECTION_SETTING = 'header_sortable_section_main_upper_responsive';
    private const LOWER_RESPONSIVE_SECTION_SETTING = 'header_sortable_section_main_lower_responsive';

    private const TOKENS_SETTING              = 'tokens';
    private const LOWER_AREA_SCOPE             = 'scope:s-header-flexible-lower';
    private const UPPER_ITEMS = ['logotype', 'language', 'drawer', 'user'];
    private const LOWER_ITEMS = ['primary'];
    private const UPPER_RESPONSIVE_ITEMS = ['logotype', 'language', 'drawer'];

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
        if ((string) $this->wpService->getThemeMod(self::HEADER_APPEARANCE_SETTING, '') !== 'business') {
            return;
        }

        $this->applyTemplate();
    }

    /**
     * Apply the business flexible template configuration.
     */
    public function applyTemplate(): void
    {
        $this->wpService->setThemeMod(self::HEADER_APPEARANCE_SETTING, 'flexible');
        $this->wpService->setThemeMod(self::UPPER_SECTION_SETTING, self::UPPER_ITEMS);
        $this->wpService->setThemeMod(self::LOWER_SECTION_SETTING, self::LOWER_ITEMS);
        $this->wpService->setThemeMod(self::UPPER_RESPONSIVE_SECTION_SETTING, self::UPPER_RESPONSIVE_ITEMS);
        $this->wpService->setThemeMod(self::LOWER_RESPONSIVE_SECTION_SETTING, []);

        $storage = $this->getNormalizedHiddenStorage();
        $storage[self::UPPER_SECTION_SETTING] = $this->buildDefaultItemOptions(self::UPPER_ITEMS, 'right');
        $storage[self::UPPER_SECTION_SETTING]['logotype']['align'] = 'left';
        $storage[self::LOWER_SECTION_SETTING] = $this->buildDefaultItemOptions(self::LOWER_ITEMS, 'right');
        $storage[self::UPPER_RESPONSIVE_SECTION_SETTING] = $this->buildDefaultItemOptions(self::UPPER_RESPONSIVE_ITEMS, 'right');
        $storage[self::UPPER_RESPONSIVE_SECTION_SETTING]['logotype']['align'] = 'left';
        $storage[self::LOWER_RESPONSIVE_SECTION_SETTING] = [];

        $legacyAlignment = (string) $this->wpService->getThemeMod('business_header_alignment', 'business-gap');
        $menuAlignments = [
            'business-left' => 'left',
            'business-right' => 'right',
            'business-gap' => 'right',
        ];

        if (isset($menuAlignments[$legacyAlignment])) {
            $storage[self::LOWER_SECTION_SETTING]['primary']['align'] = $menuAlignments[$legacyAlignment];
        }

        $this->wpService->setThemeMod(self::HEADER_HIDDEN_STORAGE_SETTING, json_encode($storage) ?: '{}');

        $this->applyDefaultLowerAreaPadding();
    }

    /**
     * Disable vertical padding on the flexible lower area by default.
     *
     * The lower area holds the primary navigation bar; removing its top/bottom
     * padding gives the nav bar a full-bleed appearance matching the original
     * business header design.
     */
    private function applyDefaultLowerAreaPadding(): void
    {
        $raw    = $this->wpService->getThemeMod(self::TOKENS_SETTING, null);
        $tokens = $this->parseTokens($raw);

        if (isset($tokens['component'][self::LOWER_AREA_SCOPE]['header']['--padding-y-enabled'])) {
            return;
        }

        $tokens['component'][self::LOWER_AREA_SCOPE]['header']['--padding-y-enabled'] = '0';

        $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
    }

    /**
     * Parse stored design tokens or return a safe default structure.
     *
     * @param mixed $raw Raw theme mod value.
     *
     * @return array<string, mixed>
     */
    private function parseTokens(mixed $raw): array
    {
        $default = ['token' => [], 'component' => []];

        if (!is_string($raw) || trim($raw) === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $default;
    }

    /**
     * Resolve hidden storage as an associative array.
     *
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
     * Build default item options for hidden storage.
     *
     * @param array<int, string> $items Items to include.
     * @param string $align Default alignment.
     *
     * @return array<string, array<string, string>>
     */
    private function buildDefaultItemOptions(array $items, string $align): array
    {
        $options = [];

        foreach ($items as $item) {
            $options[$item] = [
                'align' => $align,
                'margin' => 'none',
            ];
        }

        return $options;
    }
}
