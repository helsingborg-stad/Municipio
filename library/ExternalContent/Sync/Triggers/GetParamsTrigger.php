<?php

namespace Municipio\ExternalContent\Sync\Triggers;

use WpService\Contracts\AddAction;
use WpService\Contracts\DoAction;
use WpService\Contracts\IsAdmin;

class GetParamsTrigger implements TriggerInterface
{
    public const GET_PARAM_TRIGGER   = 'sync_external_content';
    public const GET_PARAM_POST_TYPE = 'post_type';
    public const GET_PARAM_POST_ID   = 'sync_post_id';


    public function __construct(
        private AddAction&DoAction&IsAdmin $wpService,
        private TriggerInterface $inner = new NullTrigger()
    ) {
    }

    public function trigger(): void
    {
        if ($this->shouldTrigger()) {
            $this->wpService->addAction('admin_init', function () {
                $postType = $_GET[self::GET_PARAM_POST_TYPE];
                $postId   = isset($_GET[self::GET_PARAM_POST_ID]) ? $_GET[self::GET_PARAM_POST_ID] : null;

                /**
                 * Fires when external content should be synced.
                 *
                 * @param string $postType The post type to sync.
                 * @param int|null $postId The post id to sync.
                 */
                $this->wpService->doAction('Municipio/ExternalContent/Sync', $postType, $postId);
            });
        }

        $this->inner->trigger();
    }

    private function shouldTrigger(): bool
    {
        if (
            isset($_GET[self::GET_PARAM_TRIGGER]) &&
            isset($_GET[self::GET_PARAM_POST_TYPE])
        ) {
            return true;
        }

        return false;
    }
}
