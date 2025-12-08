<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * Maps archive controller data to PostsList config objects
 */
class ArchiveDataToPostsListConfigMapper implements PostsListConfigMapperInterface
{
    public function map(mixed $sourceData): PostsListConfigDTO
    {
        // $sourceData is expected to be the $this->data array from Archive.php, with 'wpTaxonomies' key
        $data = $sourceData;
        $wpTaxonomies = $data['wpTaxonomies'] ?? [];
        $queryVarsPrefix = $data['queryVarsPrefix'] ?? 'archive_';

        $wpService = $data['wpService'] ?? null;
        $wpdb = $data['wpdb'] ?? null;
        if (!$wpService) {
            // Fallback to global if not provided
            $wpService = $GLOBALS['wpService'] ?? null;
        }
        if (!$wpdb) {
            $wpdb = $GLOBALS['wpdb'] ?? null;
        }

        // Use the same factories as ArchivePostsListFactory
        $appearanceConfig = (new \Municipio\Controller\Archive\AppearanceConfigFactory())->create($data);
        $filterConfig = (new \Municipio\Controller\Archive\FilterConfigFactory(
            $data,
            $wpTaxonomies,
            $wpService,
            new \Municipio\PostsList\QueryVars\QueryVars($queryVarsPrefix),
        ))->create();
        $getPostsConfig = (new \Municipio\Controller\Archive\GetPostsConfigFactory(
            $data,
            $filterConfig,
            new \Municipio\PostsList\QueryVars\QueryVars($queryVarsPrefix),
            $wpService,
        ))->create();

        return new PostsListConfigDTO($getPostsConfig, $appearanceConfig, $filterConfig, $queryVarsPrefix);
    }
}
