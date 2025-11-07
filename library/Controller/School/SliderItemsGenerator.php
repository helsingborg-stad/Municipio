<?php

namespace Municipio\Controller\School;

use Municipio\Schema\Preschool;
use Municipio\Schema\ImageObject;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Integrations\Component\ImageResolver;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\VideoObject;
use WP_Embed;

class SliderItemsGenerator
{
    public function __construct(private ElementarySchool|Preschool $school, private WP_Embed $wpEmbed)
    {
    }

    public function generate(): mixed
    {
        return [
            ...$this->getSliderImageItems(...EnsureArrayOf::ensureArrayOf($this->school->getProperty('image'), ImageObject::class)),
            ...$this->getSliderVideoItems(...EnsureArrayOf::ensureArrayOf($this->school->getProperty('video'), VideoObject::class)),
        ];
    }

    private function getSliderImageItems(ImageObject ...$images): array
    {
        $images = array_map(
            fn(ImageObject $imageObject) => $this->getSliderImageItem($imageObject),
            $images
        );

        return !empty($images) ? [
            'imageItems' => $images
        ] : [];
    }

    private function getSliderImageItem(ImageObject $imageObject): ?array
    {
        if (empty($imageObject->getProperty('@id'))) {
            return null;
        }

        return [
            'image'          => ImageComponentContract::factory($imageObject->getProperty('@id'), [760, false], new ImageResolver()),
            'text'           => $imageObject->getProperty('caption') ?: null,
            'layout'         => 'bottom',
            'containerColor' => 'darkest'
        ];
    }

    private function getSliderVideoItems(VideoObject ...$videoObjects): array
    {
        $videos = array_map(
            fn(VideoObject $videoObject) => $this->getSliderVideoItem($videoObject),
            $videoObjects
        );

        return !empty($videos) ? [
            'videoItems' => array_filter($videos)
        ] : [];
    }

    /**
     * Get item from video object.
     * Supports video objects with a url property only.
     *
     * @param VideoObject $videoObject
     * @return array|null
     */
    private function getSliderVideoItem(VideoObject $videoObject): ?array
    {
        if (empty($videoObject->getProperty('url'))) {
            return null;
        }

        return [
            'embed' => $this->getEmbeddedVideoHtml($videoObject->getProperty('url')),
        ];
    }

    private function getEmbeddedVideoHtml(string $url): string
    {
        return $this->wpEmbed->run_shortcode('[embed]' . esc_url($url) . '[/embed]');
    }
}
