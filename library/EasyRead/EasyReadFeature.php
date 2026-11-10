<?php

declare(strict_types=1);

namespace Municipio\EasyRead;

use AcfService\AcfService;
use Municipio\EasyRead\Admin\AcfLocationRules;
use Municipio\EasyRead\Admin\OptionsPage;
use Municipio\EasyRead\Admin\PostTypeFieldChoices;
use Municipio\EasyRead\Config\EasyReadConfig;
use Municipio\EasyRead\Content\AcfAlternativeContentRepository;
use Municipio\EasyRead\Content\ContentSwitcher;
use Municipio\EasyRead\Content\CurrentUrl;
use Municipio\EasyRead\Migration\LegacyPluginConflictGuard;
use Municipio\EasyRead\Request\NativeReadableRequest;
use WpService\WpService;

/**
 * Composes the built-in easy-read feature.
 */
final class EasyReadFeature
{
    public function __construct(
        private WpService $wpService,
        private AcfService $acfService,
    ) {}

    public function enable(): void
    {
        if ((new LegacyPluginConflictGuard($this->wpService))->deactivateConflictingPlugin()) {
            return;
        }

        $config = new EasyReadConfig($this->acfService);

        $this->wpService->addFilter('Municipio/AcfExportManager/autoExport', [$this, 'registerAcfExports']);
        (new OptionsPage($this->wpService, $this->acfService))->addHooks();
        (new AcfLocationRules($this->wpService, $config))->addHooks();
        (new PostTypeFieldChoices($this->wpService))->addHooks();
        (new ContentSwitcher(
            $this->wpService,
            new AcfAlternativeContentRepository($this->acfService, $config),
            $config,
            new NativeReadableRequest($config),
            new CurrentUrl($this->wpService),
        ))->addHooks();
    }

    /**
     * Keeps Easy Read field groups in Municipio's shared ACF import/export flow.
     *
     * @param array<string, string> $autoExportIds
     * @return array<string, string>
     */
    public function registerAcfExports(array $autoExportIds): array
    {
        $autoExportIds['easy-reading'] = 'group_58eb4fce51bb7';
        $autoExportIds['easy-reading-options'] = 'group_58eb9450b0a9f';

        return $autoExportIds;
    }
}
