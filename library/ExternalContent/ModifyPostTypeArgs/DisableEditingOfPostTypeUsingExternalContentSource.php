<?php

namespace Municipio\ExternalContent\ModifyPostTypeArgs;

use WpService\Contracts\AddFilter;
use WpService\Contracts\CurrentUserCan;
use WpService\Contracts\DoingCron;

/**
 * Class DisableEditingOfPostTypeUsingExternalContentSource
 */
class DisableEditingOfPostTypeUsingExternalContentSource extends ModifyPostTypeArgs
{
    /**
     * Constructor.
     */
    public function __construct(private array $configs, private AddFilter&DoingCron&CurrentUserCan $wpService)
    {
        parent::__construct($wpService);
    }

    /**
     * @inheritDoc
     */
    public function modify(array $args, string $postType): array
    {
        if (
            empty($this->configs) ||
            $this->wpService->doingCron() ||
            $this->wpService->currentUserCan('activate_plugins', null)
        ) {
            return $args;
        }

        foreach ($this->configs as $config) {
            if ($config->getPostType() !== $postType) {
                continue;
            }

            $args['capabilities'] = [
                'edit_post'     => 'do_not_allow',
                'delete_post'   => 'do_not_allow',
                'publish_posts' => 'do_not_allow',
                'create_posts'  => 'do_not_allow',
            ];

            break;
        }

        return $args;
    }
}
