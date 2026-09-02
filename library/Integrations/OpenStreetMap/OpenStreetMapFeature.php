<?php

namespace Municipio\Integrations\OpenStreetMap;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class OpenStreetMapFeature implements Hookable
{
    public function __construct(private AddFilter $wpService)
    {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('WpSecurity/Csp', function (array $domains) {
            if (!isset($domains['img-src'])) {
                $domains['img-src'] = [];
            }

            $domains['img-src'][] = 'https://tile.openstreetmap.bzh';
            $domains['img-src'][] = 'https://*.tile.openstreetmap.fr';
            $domains['img-src'][] = 'https://tile.openstreetmap.org';
    
            return $domains;
        });
    }
}
