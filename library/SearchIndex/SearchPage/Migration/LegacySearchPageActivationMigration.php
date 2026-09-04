<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\SearchPage\Migration;

use AcfService\Contracts\UpdateField;
use WpService\Contracts\GetOption;

/**
 * Enables SearchPage when the replaced add-on was active before migration.
 */
class LegacySearchPageActivationMigration
{
    public const LEGACY_ACTIVATION_OPTION = 'municipio_search_index_legacy_search_page_was_active';

    public function __construct(
        private GetOption $wpService,
        private UpdateField $acfService,
    ) {}

    /**
     * Migrate the captured legacy add-on activation state.
     */
    public function migrate(): void
    {
        if (!$this->wpService->getOption(self::LEGACY_ACTIVATION_OPTION, false)) {
            return;
        }

        $this->acfService->updateField('search_index_search_page_enabled', true, 'option');
    }
}