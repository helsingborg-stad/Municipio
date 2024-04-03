<?php 

namespace Municipio\Schema\PostDecorator\Place;

use Municipio\Schema\PostDecorator\SchemaDecoratorInterface;
use Municipio\Schema\PostDecorator\Place\CreateMarkerData;

class Place implements SchemaDecoratorInterface {
    private CreateMarkerData $createMarkerData;

    public function __construct(private array $schemaData = []){
        $this->createMarkerData = new CreateMarkerData($this->schemaData);
    }

    public function appendData(\WP_Post $post): \WP_Post
    {
        if (empty($this->schemaData) || empty($this->schemaData['geo'])) {
            return $post;
        }

        $post->schemaData['place'] = [];

        $post->schemaData['place']['geo'] = $this->schemaData['geo'] ?? [];
        $post->schemaData['place']['pin'] = $this->createMarkerData->addMarker($post);

        return $post;
    }
}