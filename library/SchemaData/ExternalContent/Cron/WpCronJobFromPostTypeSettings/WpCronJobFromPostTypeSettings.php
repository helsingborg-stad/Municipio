<?php

namespace Municipio\SchemaData\ExternalContent\Cron\WpCronJobFromPostTypeSettings;

use Municipio\SchemaData\ExternalContent\Config\SourceConfigInterface;
use WpCronService\WpCronJob\WpCronJobInterface;
use WpService\Contracts\DoAction;

/**
 * Class WpCronJobFromPostTypeSettings
 * This class is responsible for creating a cron job from a post type setting.
 */
class WpCronJobFromPostTypeSettings implements WpCronJobInterface
{
    /**
     * WpCronJobFromPostTypeSettings constructor.
     *
     * @param \Municipio\ExternalContent\Config\SourceConfigInterface $sourceConfig
     * @param DoAction $wpService
     */
    public function __construct(
        private SourceConfigInterface $sourceConfig,
        private DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getHookName(): string
    {
        return $this->sourceConfig->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getSchedule(): string
    {
        return $this->sourceConfig->getAutomaticImportSchedule();
    }

    /**
     * @inheritDoc
     */
    public function getFirstOccurenceTimestamp(): int
    {
        return time();
    }

    /**
     * @inheritDoc
     */
    public function getCallback(): callable
    {
        return fn () =>
            $this->wpService->doAction(
                'Municipio/ExternalContent/Sync',
                $this->sourceConfig->getPostType()
            );
    }

    /**
     * @inheritDoc
     */
    public function getArgs(): array
    {
        return [];
    }
}
