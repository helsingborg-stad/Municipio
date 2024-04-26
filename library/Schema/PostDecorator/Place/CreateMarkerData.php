<?php

namespace Municipio\Schema\PostDecorator\Place;

class CreateMarkerData {
    public function __construct(private array $schemaData) {}

    public function addMarker(\WP_Post $post, ?string $googleMapsLink): ?array 
    {
        if (empty($this->schemaData['geo']['lat'] || empty($post->location['lng']))) {
            return null;
        }

        $pin = [
            'lat'     => $this->schemaData['geo']['lat'],
            'lng'     => $this->schemaData['geo']['lng'],
            'tooltip' => [
                'title'      => $post->post_title ?? '',
                'excerpt'    => $post->post_excerpt ?? '',
                'url'        => $post->permalink ?? '',
                'directions' => [
                    'url'   => $googleMapsLink ?? '',
                    'label' => __('Get directions on Google Maps', 'municipio'),
                ]
            ],
        ];

        // Add icon to pin
        if (!empty($post->termIcon)) {
            $pin['icon'] = $post->termIcon;
        }

        return $pin;
    }
}