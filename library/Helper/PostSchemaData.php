<?php

namespace Municipio\Helper;

class PostSchemaData {
    public function __construct(private \WP_Post $post, private array $schemaData = []){}

    public function addSchemaDataToPost() {
        if (!empty($this->schemaData['geo'])) {
            $this->post->location = $this->schemaData['geo'];
            $this->post->location['pin'] = \Municipio\Helper\Location::createMapMarker($this->post);
        }
        return $this->post;
    }
}