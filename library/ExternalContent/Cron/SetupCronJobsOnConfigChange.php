<?php

namespace Municipio\ExternalContent\Cron;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use Municipio\ExternalContent\Cron\WpCronJobFromPostTypeSettings\WpCronJobFromPostTypeSettings;
use Municipio\HooksRegistrar\Hookable;
use WpCronService\WpCronJobManagerInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;

class SetupCronJobsOnConfigChange implements Hookable
{
    public function __construct(
        private ExternalContentConfigInterface $config,
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
        foreach ($this->config->getEnabledPostTypes() as $postType) {
            $postTypeSettings = $this->config->getPostTypeSettings($postType);
            $cronJob          = new WpCronJobFromPostTypeSettings($postTypeSettings, $this->wpService);

            $this->cronJobsManager->register($cronJob);
        }
    }
}
