<?php

namespace Municipio\ExternalContent\ModifyPostTypeArgs;

use WpService\Contracts\AddFilter;

class DisableEditingOfPostTypeUsingExternalContentSource extends ModifyPostTypeArgs
{
    /**
     * @param \Municipio\ExternalContent\Config\ExternalContentConfigInterface[] $config
     * @param \WpService\Contracts\AddFilter $wpService
     */
    public function __construct(private array $configs, private AddFilter $wpService)
    {
        parent::__construct($wpService);
    }

    public function modify(array $args, string $postType): array
    {
        if (empty($this->configs)) {
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
