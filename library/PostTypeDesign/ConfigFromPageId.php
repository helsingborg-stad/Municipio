<?php

namespace Municipio\PostTypeDesign;

use WpService\Contracts\IsWPError;
use WpService\Contracts\WpRemoteGet;
use WpService\Contracts\WpRemoteRetrieveBody;

class ConfigFromPageId
{
    private $apiUrl = 'https://customizer.municipio.tech/id/';

    public function __construct(private IsWPError&WpRemoteGet&WpRemoteRetrieveBody $wpService)
    {
    }

    public function get($designId): array
    {
        $response = $this->wpService->wpRemoteGet($this->apiUrl . $designId);
        if ($this->wpService->isWPError($response)) {
            return [];
        } else {
            $body = $this->wpService->wpRemoteRetrieveBody($response);
            $body = json_decode($body, true);

            return [
                !empty($body['mods']) ? $body['mods'] : [],
                !empty($body['css']) ? $body['css'] : null
            ];
        }
    }
}
