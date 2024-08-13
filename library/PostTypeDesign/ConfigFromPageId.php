<?php

namespace Municipio\PostTypeDesign;

use WpService\Contracts\IsWPError;
use WpService\Contracts\RemoteGet;
use WpService\Contracts\RemoteRetrieveBody;

/**
 * Class ConfigFromPageId
 *
 * Retrieves configuration data from a design ID.
 */
class ConfigFromPageId implements ConfigFromPageIdInterface
{
    /**
     * ConfigFromPageId constructor.
     *
     * @param IsWPError&RemoteGet&RemoteRetrieveBody $wpService The WordPress service instance.
     */
    public function __construct(private IsWPError&RemoteGet&RemoteRetrieveBody $wpService, private string $apiUrl = '')
    {
    }

    /**
     * Retrieves the configuration data for a given design ID.
     *
     * @param string $designId The design ID.
     * @return array An array containing the configuration data and CSS code (if available).
     */
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
