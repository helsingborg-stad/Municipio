<?php

namespace Municipio\ExternalContent\Cron;

use Municipio\Config\ConfigFactoryInterface;
use Municipio\ExternalContent\Cron\WpCronJobFromPostTypeSettings\WpCronJobFromPostTypeSettings;
use Municipio\HooksRegistrar\Hookable;
use WpCronService\WpCronJobManagerInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;

class SetupCronJobsOnConfigChange implements Hookable
{
    public function __construct(
        private ConfigFactoryInterface $configFactory,
        private WpCronJobManagerInterface $cronJobsManager,
        private AddAction&DoAction $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'setupCronJobs']);
    }

    public function setupCronJobs(): void
    {
        $config = $this->configFactory->createConfig()->getExternalContentConfig();

        foreach ($config->getEnabledPostTypes() as $postType) {
            $postTypeSettings = $config->getPostTypeSettings($postType);
            $cronJob          = new WpCronJobFromPostTypeSettings($postTypeSettings, $this->wpService);

            $this->cronJobsManager->register($cronJob);
        }
    }
}
