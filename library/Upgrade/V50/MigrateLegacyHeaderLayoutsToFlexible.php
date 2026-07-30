<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use Municipio\Upgrade\V50\MigrateLegacyBusinessHeaderToFlexible;
use Municipio\Upgrade\V50\MigrateLegacyCasualHeaderToFlexible;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Runs legacy header layout migrations to flexible templates.
 */
class MigrateLegacyHeaderLayoutsToFlexible
{
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
        $detectedLayout = $this->detectLegacyLayout();

        if ($detectedLayout === 'casual') {
            (new MigrateLegacyCasualHeaderToFlexible($this->wpService))->applyTemplate();
            return;
        }

        if ($detectedLayout === 'business') {
            (new MigrateLegacyBusinessHeaderToFlexible($this->wpService))->applyTemplate();
        }
    }

    /**
     * Detect which legacy layout template should be converted to flexible.
     */
    private function detectLegacyLayout(): ?string
    {
        $appearance = strtolower((string) $this->wpService->getThemeMod('header_apperance', ''));

        if (in_array($appearance, ['casual', 'business'], true)) {
            return $appearance;
        }

        if (!in_array($appearance, ['', 'flexible'], true)) {
            return null;
        }

        $businessAlignment = strtolower((string) $this->wpService->getThemeMod('business_header_alignment', ''));
        if (in_array($businessAlignment, ['business-left', 'business-right', 'business-gap'], true)) {
            return 'business';
        }

        $casualAlignment = strtolower((string) $this->wpService->getThemeMod('casual_header_alignment', ''));
        if (in_array($casualAlignment, ['casual-left', 'casual-center', 'casual-right'], true)) {
            return 'casual';
        }

        $hiddenStorage = $this->getHiddenStorage();
        $upperHidden = $hiddenStorage['header_sortable_section_main_upper'] ?? [];
        if (is_array($upperHidden) && array_key_exists('primary', $upperHidden)) {
            return 'casual';
        }

        $upperItems = $this->normalizeSectionItems($this->wpService->getThemeMod('header_sortable_section_main_upper', []));
        $lowerItems = $this->normalizeSectionItems($this->wpService->getThemeMod('header_sortable_section_main_lower', []));

        $isSplitDefaultShape = $upperItems === ['logotype', 'language', 'drawer', 'user'] && $lowerItems === ['primary'];

        if (!$isSplitDefaultShape) {
            return null;
        }

        $headerBackground = strtolower((string) $this->wpService->getThemeMod('header_background', ''));

        return $headerBackground === 'primary' ? 'casual' : 'business';
    }

    /**
     * @return array<string, mixed>
     */
    private function getHiddenStorage(): array
    {
        $storage = $this->wpService->getThemeMod('header_sortable_hidden_storage', []);

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
    private function normalizeSectionItems(mixed $items): array
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
}
