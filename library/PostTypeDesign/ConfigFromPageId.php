<?php

namespace Municipio\PostTypeDesign;

use WpService\Contracts\IsWPError;
use WpService\Contracts\RemoteGet;
use WpService\Contracts\RemoteRetrieveBody;

class ConfigFromPageId implements ConfigFromPageIdInterface
{
    private $apiUrl = 'https://customizer.municipio.tech/id/';

    public function __construct(private IsWPError&RemoteGet&RemoteRetrieveBody $wpService)
    {
    }

    public function get(string $designId): array
    {
        $response = $this->wpService->remoteGet($this->apiUrl . $designId);
        if ($this->wpService->isWPError($response)) {
            return [[], null];
        } else {
            $body = $this->wpService->remoteRetrieveBody($response);
            $body = json_decode($body, true);

            return [
                !empty($body['mods']) ? $body['mods'] : [],
                !empty($body['css']) ? $body['css'] : null
            ];
        }
    }
}
