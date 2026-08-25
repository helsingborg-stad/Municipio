<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V52;

use Municipio\Upgrade\V50\MigrateLegacyBusinessHeaderToFlexible;
use Municipio\Upgrade\V50\MigrateLegacyCasualHeaderToFlexible;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;
use WpService\Contracts\WpUpdatePost;

/**
 * Repairs malformed header sortable data from legacy migrations.
 *
 * Segmented into V52 to avoid overlapping responsibilities in V50/V51.
 */
class MigrateHeaderSortableDataIntegrity
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

    /** @var array<string, array<string, array<int, string>>> */
    private const LEGACY_TEMPLATE_SECTIONS = [
        'business' => [
            'header_sortable_section_main_upper' => ['logotype', 'language', 'drawer', 'user'],
            'header_sortable_section_main_lower' => ['primary'],
            'header_sortable_section_main_upper_responsive' => ['logotype', 'language', 'drawer'],
            'header_sortable_section_main_lower_responsive' => [],
        ],
        'casual' => [
            'header_sortable_section_main_upper' => ['logotype', 'primary'],
            'header_sortable_section_main_lower' => [],
            'header_sortable_section_main_upper_responsive' => ['logotype', 'drawer'],
            'header_sortable_section_main_lower_responsive' => [],
        ],
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
        private readonly GetThemeMod&SetThemeMod&GetPosts&WpUpdatePost $wpService,
    ) {}

    /**
     * Run migration.
     */
    public function migrate(): void
    {
        $appearance = strtolower((string) $this->wpService->getThemeMod(self::HEADER_APPEARANCE_SETTING, ''));

        if ($appearance === 'business') {
            (new MigrateLegacyBusinessHeaderToFlexible($this->wpService))->applyTemplate();
            $this->applyLegacyTemplateSectionShapes('business');
            $this->normalizeCustomizerChangesetDrafts();
            return;
        }

        if ($appearance === 'casual') {
            (new MigrateLegacyCasualHeaderToFlexible($this->wpService))->applyTemplate();
            $this->applyLegacyTemplateSectionShapes('casual');
            $this->normalizeCustomizerChangesetDrafts();
            return;
        }

        if ($appearance !== 'flexible') {
            return;
        }

        $this->normalizeSectionSettingShapes();

        $hiddenStorage = $this->getNormalizedHiddenStorage();
        $storageChanged = false;

        foreach (self::SECTION_SETTINGS as $sectionSetting) {
            $items = $this->normalizeItems($this->wpService->getThemeMod($sectionSetting, []));

            if (empty($items)) {
                $recoveredItems = $this->getOrderedItemsFromHiddenStorage($hiddenStorage[$sectionSetting] ?? null);

                if (!empty($recoveredItems)) {
                    $this->persistSectionItems($sectionSetting, $recoveredItems);
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

        $this->normalizeCustomizerChangesetDrafts();
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

        if (!str_starts_with(ltrim($items), '[')) {
            $explodedItems = str_contains($items, ',') ? explode(',', $items) : [$items];

            return array_values(array_filter(
                array_map(static fn(string $item): string => trim($item), $explodedItems),
                static fn(string $item): bool => $item !== '',
            ));
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

    /**
     * Normalize sortable section settings to JSON-string arrays.
     */
    private function normalizeSectionSettingShapes(): void
    {
        foreach (self::SECTION_SETTINGS as $sectionSetting) {
            $items = $this->normalizeItems($this->wpService->getThemeMod($sectionSetting, []));
            $this->persistSectionItems($sectionSetting, $items);
        }
    }

    /**
     * Persist sortable section values as JSON-string arrays.
     *
     * @param array<int, string> $items
     */
    private function persistSectionItems(string $sectionSetting, array $items): void
    {
        $this->wpService->setThemeMod($sectionSetting, json_encode($items) ?: '[]');
    }

    /**
     * Persist known V50 legacy template section values in normalized JSON shape.
     */
    private function applyLegacyTemplateSectionShapes(string $appearance): void
    {
        $templateSections = self::LEGACY_TEMPLATE_SECTIONS[$appearance] ?? [];

        foreach ($templateSections as $sectionSetting => $items) {
            $this->persistSectionItems($sectionSetting, $items);
        }
    }

    /**
     * Normalize header sortable values stored in customizer changeset drafts.
     */
    private function normalizeCustomizerChangesetDrafts(): void
    {
        try {
            $changesets = $this->wpService->getPosts([
                'post_type' => 'customize_changeset',
                'post_status' => ['draft', 'auto-draft'],
                'numberposts' => 25,
                'orderby' => 'ID',
                'order' => 'DESC',
            ]);
        } catch (\TypeError) {
            return;
        }

        foreach ($changesets as $changeset) {
            $postId = is_object($changeset) && isset($changeset->ID) ? (int) $changeset->ID : 0;
            $postContent = is_object($changeset) && isset($changeset->post_content) ? (string) $changeset->post_content : '';

            if ($postId <= 0 || trim($postContent) === '') {
                continue;
            }

            $decoded = json_decode($postContent, true);

            if (!is_array($decoded)) {
                continue;
            }

            $changed = false;

            foreach ($decoded as $settingKey => &$settingData) {
                if (!is_string($settingKey) || !is_array($settingData)) {
                    continue;
                }

                if (!in_array($this->extractSettingName($settingKey), self::SECTION_SETTINGS, true)) {
                    continue;
                }

                $currentValue = $settingData['value'] ?? null;
                $normalizedItems = $this->normalizeItems($currentValue);
                $normalizedValue = json_encode($normalizedItems) ?: '[]';

                if (($settingData['value'] ?? null) !== $normalizedValue) {
                    $settingData['value'] = $normalizedValue;
                    $changed = true;
                }
            }
            unset($settingData);

            if (!$changed) {
                continue;
            }

            $nextContent = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if (!is_string($nextContent)) {
                continue;
            }

            $this->wpService->wpUpdatePost([
                'ID' => $postId,
                'post_content' => addslashes($nextContent),
            ]);
        }
    }

    /**
     * Convert "stylesheet::setting" keys to plain setting names.
     */
    private function extractSettingName(string $settingKey): string
    {
        $separatorPosition = strpos($settingKey, '::');

        if ($separatorPosition === false) {
            return $settingKey;
        }

        return substr($settingKey, $separatorPosition + 2);
    }
}
