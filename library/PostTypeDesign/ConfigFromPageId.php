<?php

namespace Municipio\PostTypeDesign;

class ConfigFromPageId {

    private $apiUrl = 'https://customizer.municipio.tech/id/';

    public function __construct(private string $designId) {}

    public function get(): array
    {
        $response = wp_remote_get($this->apiUrl . $this->designId);
        if (is_wp_error($response)) {
            return [];
        } else {
            $body = wp_remote_retrieve_body($response);
            $body = json_decode($body, true);

            return [
                !empty($body['mods']) ? $body['mods'] : [], 
                !empty($body['css']) ? $body['css'] : null
            ];
        }
    }
}