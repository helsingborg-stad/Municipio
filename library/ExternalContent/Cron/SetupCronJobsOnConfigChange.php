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
    /**
     * @param use \Municipio\ExternalContent\Config\SourceConfigInterface[] $sourceConfigs
     * @param WpCronJobManagerInterface $cronJobsManager
     * @param AddAction&DoAction $wpService
     */
    public function __construct(
        private array $sourceConfigs,
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
        foreach ($this->sourceConfigs as $sourceConfig) {
            $cronJob = new WpCronJobFromPostTypeSettings($sourceConfig, $this->wpService);

            $this->cronJobsManager->register($cronJob);
        }
    }
}
