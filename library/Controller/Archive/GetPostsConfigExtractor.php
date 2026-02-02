<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * Extracts async configuration data from get posts config.
 *
 * Follows Single Responsibility Principle - only responsible for extracting posts config data.
 */
class GetPostsConfigExtractor implements AsyncConfigExtractorInterface
{
    private GetPostsConfigInterface $getPostsConfig;

    public function __construct(GetPostsConfigInterface $getPostsConfig)
    {
        $this->getPostsConfig = $getPostsConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(): array
    {
        $data = [];

        // Extract post types
        if (method_exists($this->getPostsConfig, 'getPostTypes')) {
            $postTypes = $this->getPostsConfig->getPostTypes();
            $data['postType'] = !empty($postTypes) ? $postTypes[0] : null;
        }

        // Extract posts per page
        if (method_exists($this->getPostsConfig, 'getPostsPerPage')) {
            $data['postsPerPage'] = $this->getPostsConfig->getPostsPerPage() ?? 10;
        }

        // Extract pagination enabled
        if (method_exists($this->getPostsConfig, 'paginationEnabled')) {
            $data['paginationEnabled'] = $this->getPostsConfig->paginationEnabled() ?? true;
        }

        return $data;
    }
}
