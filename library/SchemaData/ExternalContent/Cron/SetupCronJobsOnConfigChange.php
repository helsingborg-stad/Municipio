<?php

namespace Municipio\SchemaData\ExternalContent\Cron;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use Municipio\SchemaData\ExternalContent\Cron\WpCronJobFromPostTypeSettings\WpCronJobFromPostTypeSettings;
use Municipio\HooksRegistrar\Hookable;
use WpCronService\WpCronJobManagerInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;

/**
 * Sets up WP Cron jobs when external content configuration changes.
 *
 * Registers cron jobs based on provided source configs and hooks into WordPress actions.
 */
class SetupCronJobsOnConfigChange implements Hookable
{
    /**
     * @param use \Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface[] $sourceConfigs
     * @param WpCronJobManagerInterface $cronJobsManager
     * @param AddAction&DoAction $wpService
     */
    public function __construct(
        private array $sourceConfigs,
        private WpCronJobManagerInterface $cronJobsManager,
        private AddAction&DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('init', [$this, 'setupCronJobs']);
    }

    /**
     * Setup cron jobs based on source configs.
     */
    public function setupCronJobs(): void
    {
        foreach ($this->sourceConfigs as $sourceConfig) {
            $cronJob = new WpCronJobFromPostTypeSettings($sourceConfig, $this->wpService);

            $this->cronJobsManager->register($cronJob);
        }
    }
}
