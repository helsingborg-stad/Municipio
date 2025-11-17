<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use WpService\Contracts\GetPostTypeArchiveLink;
use WpService\Contracts\GetQueriedObject;
use WpService\Contracts\GetTermLink;
use WpService\Contracts\HomeUrl;

class MapResetUrl
{
    public function __construct(private HomeUrl&GetPostTypeArchiveLink&GetQueriedObject&GetTermLink $wpService)
    {
    }

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

    private function getPostType(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
