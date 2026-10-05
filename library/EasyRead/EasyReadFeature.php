<?php

declare(strict_types=1);

namespace Municipio\EasyRead;

use AcfService\AcfService;
use Municipio\EasyRead\Admin\AcfLocationRules;
use Municipio\EasyRead\Admin\FieldGroups;
use Municipio\EasyRead\Admin\OptionsPage;
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

        (new OptionsPage($this->wpService, $this->acfService))->addHooks();
        (new FieldGroups($this->wpService, $this->acfService))->addHooks();
        (new AcfLocationRules($this->wpService, $config))->addHooks();
        (new ContentSwitcher(
            $this->wpService,
            new AcfAlternativeContentRepository($this->acfService, $config),
            $config,
            new NativeReadableRequest($config),
            new CurrentUrl($this->wpService),
        ))->addHooks();
    }
}
