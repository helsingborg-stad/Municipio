<?php

namespace Municipio\Config\Features\ExternalContent\Contracts;

use Municipio\Config\Features\ExternalContent\ExternalContentPostTypeSettings\ExternalContentPostTypeSettingsInterface;

interface GetPostTypeSettings
{
    /**
     * Get the post type settings.
     *
     * @param string $postType The post type.
     * @return ExternalContentPostTypeSettingsInterface The post type settings.
     */
    public function getPostTypeSettings(string $postType): ExternalContentPostTypeSettingsInterface;
}
