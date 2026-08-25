<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Migration;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;

/**
 * Copies stored legacy ACF settings without overwriting SearchIndex settings.
 */
class LegacyAcfSettingsMigrator
{
    public function __construct(private GetField&UpdateField $acfService) {}

    /**
     * Migrate matching setting names between legacy and SearchIndex field groups.
     *
     * @param array<int, string> $settingNames
     * @param array<string, mixed> $legacyOptions
     */
    public function migrateFields(string $legacyPrefix, string $destinationPrefix, array $settingNames, array $legacyOptions = []): void
    {
        foreach ($settingNames as $settingName) {
            $this->migrateField($legacyPrefix . $settingName, $destinationPrefix . $settingName, $legacyOptions, $settingName);
        }
    }

    /**
     * Migrate one legacy field, using an optional legacy option fallback.
     *
     * @param array<string, mixed> $legacyOptions
     */
    public function migrateField(string $legacyField, string $destinationField, array $legacyOptions = [], ?string $legacyOptionKey = null): void
    {
        if ($this->hasValue($this->acfService->getField($destinationField, 'option'))) {
            return;
        }

        $value = $this->acfService->getField($legacyField, 'option');

        if (!$this->hasValue($value) && $legacyOptionKey !== null) {
            $value = $legacyOptions[$legacyOptionKey] ?? null;
        }

        if ($this->hasValue($value)) {
            $this->acfService->updateField($destinationField, $value, 'option');
        }
    }

    /**
     * Determine whether a setting contains a value worth preserving.
     */
    public function hasValue(mixed $value): bool
    {
        return $value !== null && $value !== false && $value !== '' && $value !== [];
    }
}