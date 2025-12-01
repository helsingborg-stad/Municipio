<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use WpService\Contracts\GetPostTypeArchiveLink;
use WpService\Contracts\GetQueriedObject;
use WpService\Contracts\GetTermLink;
use WpService\Contracts\HomeUrl;

/**
 * Maps the reset URL for archive filters based on the current request and post type
 */
class MapResetUrl
{
    /**
     * Constructor
     *
     * @param HomeUrl&GetPostTypeArchiveLink&GetQueriedObject&GetTermLink $wpService
     */
    public function __construct(private HomeUrl&GetPostTypeArchiveLink&GetQueriedObject&GetTermLink $wpService)
    {
    }

    /**
     * Maps the reset URL
     *
     * @param array $data Archive configuration data
     * @return string The reset URL
     */
    public function map(array $data): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $realPath      = (string) parse_url($this->wpService->homeUrl() . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $postTypePath  = (string) parse_url($this->wpService->getPostTypeArchiveLink($this->getPostType($data)), PHP_URL_PATH);
            $mayBeTaxonomy = (bool)   ($realPath != $postTypePath);

            if ($mayBeTaxonomy && is_a($this->wpService->getQueriedObject(), 'WP_Term')) {
                return $this->wpService->getTermLink($this->wpService->getQueriedObject());
            }
        }

        return $this->wpService->getPostTypeArchiveLink($this->getPostType($data));
    }

    /**
     * Retrieves the post type from the data array
     *
     * @param array $data Archive configuration data
     * @return string The post type
     */
    private function getPostType(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
