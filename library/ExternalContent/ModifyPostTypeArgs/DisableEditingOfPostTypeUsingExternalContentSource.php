<?php

namespace Municipio\ExternalContent\ModifyPostTypeArgs;

use Municipio\Config\Features\ExternalContent\ExternalContentConfigInterface;
use WpService\Contracts\AddFilter;

class DisableEditingOfPostTypeUsingExternalContentSource extends ModifyPostTypeArgs
{
    public function __construct(private ExternalContentConfigInterface $config, private AddFilter $wpService)
    {
        parent::__construct($wpService);
    }

    public function modify(array $args, string $postType): array
    {
        if (empty($this->config->getEnabledPostTypes())) {
            return $args;
        }

        foreach ($this->config->getEnabledPostTypes() as $postTypeWithExternalContentSource) {
            if ($postTypeWithExternalContentSource !== $postType) {
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
