<?php

namespace Municipio\PostDecorators;

use Spatie\SchemaOrg\GeoCoordinates;
use WP_Post;

class ApplyOpenStreetMapData implements PostDecorator
{
    public function __construct(private ?PostDecorator $inner = new NullDecorator())
    {
    }

    public function apply(WP_Post $post): WP_Post
    {
        $post = $this->inner->apply($post);

        if (empty($post->schemaObject)) {
            return $post;
        }

        if (empty($post->schemaObject['geo']) || !($post->schemaObject['geo'] instanceof GeoCoordinates)) {
            return $post;
        }

        $lat            = $post->schemaObject['geo']['latitude'];
        $lng            = $post->schemaObject['geo']['longitude'];
        $googleMapsLink = $this->getGoogleMapsLink($lat, $lng);

        $post->openStreetMapData = [
            'pin'            => $this->getPin($post, $lat, $lng, $googleMapsLink),
            'googleMapsLink' => $googleMapsLink
        ];

        return $post;
    }

    private function getGoogleMapsLink(float $lat, float $lng): string
    {
        return 'https://www.google.com/maps/dir/?api=1&destination=' . $lat . ',' . $lng . '&travelmode=transit';
    }

    private function getPin(WP_Post $post, float $lat, float $lng, string $googleMapsLink): array
    {
        return [
            'icon'    => $post->termIcon ?: null,
            'lat'     => $lat,
            'lng'     => $lng,
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
    }
}
