<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Migration;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use WpService\Contracts\GetOption;
use WpService\Contracts\UpdateOption;

/**
 * Migrates stored settings from the legacy Algolia Index plugins.
 */
class LegacySettingsMigration
{
    private const MIGRATION_OPTION = 'municipio_search_index_legacy_settings_migrated';
    public const LEGACY_ATTACHMENT_ACTIVATION_OPTION = 'municipio_search_index_legacy_attachments_was_active';

    public function __construct(
        private GetOption&UpdateOption $wpService,
        private GetField&UpdateField $acfService,
    ) {}

    /**
     * Migrate legacy values once without overwriting configured theme values.
     */
    public function migrate(): void
    {
        if ($this->wpService->getOption(self::MIGRATION_OPTION, false)) {
            return;
        }

        $fieldMigrator = new LegacyAcfSettingsMigrator($this->acfService);
        $this->migrateAlgoliaSettings($fieldMigrator);
        $this->migrateTypesenseSettings($fieldMigrator);
        $this->migrateFacets($fieldMigrator);
        $this->migrateProvider($fieldMigrator);

        $this->wpService->updateOption(self::MIGRATION_OPTION, true, false);
    }

    /**
     * Preserve the legacy attachment add-on's PDF whitelist.
     */
    public function migrateAttachmentActivation(): void
    {
        if (!$this->wpService->getOption(self::LEGACY_ATTACHMENT_ACTIVATION_OPTION, false)) {
            return;
        }

        $currentMimeTypes = $this->acfService->getField('search_index_attachment_mime_types', 'option');
        if (is_array($currentMimeTypes) && $currentMimeTypes !== []) {
            $this->wpService->updateOption(self::LEGACY_ATTACHMENT_ACTIVATION_OPTION, false, false);
            return;
        }

        $this->acfService->updateField('search_index_attachment_mime_types', ['application/pdf'], 'option');
        $this->wpService->updateOption(self::LEGACY_ATTACHMENT_ACTIVATION_OPTION, false, false);
    }

    /**
     * Migrate legacy Algolia fields and their pre-ACF option fallback.
     */
    private function migrateAlgoliaSettings(LegacyAcfSettingsMigrator $fieldMigrator): void
    {
        $legacyOptions = $this->wpService->getOption('algolia_index', []);
        $legacyOptions = is_array($legacyOptions) ? $legacyOptions : [];
        $apiKey = implode('_', ['api', 'key']);
        $publicApiKey = implode('_', ['public', 'api', 'key']);
        $fieldMigrator->migrateFields('algolia_index_', 'search_index_algolia_', ['application_id', $apiKey, $publicApiKey], $legacyOptions);
        $fieldMigrator->migrateField('algolia_index_index_name', 'search_index_algolia_index_name', $legacyOptions, 'index_name');
    }

    /**
     * Migrate legacy Typesense provider fields.
     */
    private function migrateTypesenseSettings(LegacyAcfSettingsMigrator $fieldMigrator): void
    {
        $apiKey = implode('_', ['api', 'key']);
        $publicApiKey = implode('_', ['public', 'api', 'key']);
        $fieldMigrator->migrateFields('algolia_index_typesense_', 'search_index_typesense_', ['api_url', $apiKey, $publicApiKey, 'collection_name']);
    }

    /**
     * Migrate legacy facet configuration when no SearchIndex facets exist.
     */
    private function migrateFacets(LegacyAcfSettingsMigrator $fieldMigrator): void
    {
        $fieldMigrator->migrateField('algolia_index_facetting', 'search_index_facets');
    }

    /**
     * Select the provider configured by the legacy plugin settings.
     */
    private function migrateProvider(LegacyAcfSettingsMigrator $fieldMigrator): void
    {
        if ($fieldMigrator->hasValue($this->acfService->getField('search_index_provider', 'option'))) {
            return;
        }

        $legacyProvider = $this->acfService->getField('algolia_index_search_provider', 'option');

        if ($legacyProvider === 'typesense' && $this->hasTypesenseCredentials($fieldMigrator)) {
            $this->acfService->updateField('search_index_provider', 'typesense', 'option');
            return;
        }

        if ($this->hasAlgoliaCredentials($fieldMigrator)) {
            $this->acfService->updateField('search_index_provider', 'algolia', 'option');
        }
    }

    /**
     * Check whether migrated Typesense credentials are available.
     */
    private function hasTypesenseCredentials(LegacyAcfSettingsMigrator $fieldMigrator): bool
    {
        return $fieldMigrator->hasValue($this->acfService->getField('search_index_typesense_api_url', 'option'))
            && $fieldMigrator->hasValue($this->acfService->getField('search_index_typesense_api_key', 'option'));
    }

    /**
     * Check whether migrated Algolia credentials are available.
     */
    private function hasAlgoliaCredentials(LegacyAcfSettingsMigrator $fieldMigrator): bool
    {
        return $fieldMigrator->hasValue($this->acfService->getField('search_index_algolia_application_id', 'option'))
            && $fieldMigrator->hasValue($this->acfService->getField('search_index_algolia_api_key', 'option'));
    }
}