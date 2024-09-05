<?php

namespace Municipio\ExternalContent\Cron\WpCronJobFromPostTypeSettings;

use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;
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
     * @param ExternalContentPostTypeSettingsInterface $postTypeSetting
     * @param DoAction $wpService
     */
    public function __construct(
        private ExternalContentPostTypeSettingsInterface $postTypeSetting,
        private DoAction $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getHookName(): string
    {
        return $this->postTypeSetting->getPostType();
    }

    /**
     * @inheritDoc
     */
    public function getSchedule(): string
    {
        return $this->postTypeSetting->getCronSchedule();
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
                $this->postTypeSetting->getPostType()
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
