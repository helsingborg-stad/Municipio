<?php

namespace Municipio\Helper;

use Municipio\Helper\Listing as ListingHelper;

class PurposePlace
{
    public function complementPlacePost($post, $complementPost = true)
    {
        if ($complementPost) {
            $post = \Municipio\Helper\Post::preparePostObject($post);
        }

        $post->postExcerpt = self::createExcerpt($post);
        $fields = get_fields($post->id);

        $post->location = $fields['location'] ?? [];

        $directions = false;
        if (!empty($post->location['lat']) && !empty($post->location['lng'])) {
            $directions = 'https://www.google.com/maps/dir/?api=1&destination=' . $post->location['lat'] . ',' . $post->location['lng'] . '&travelmode=transit';
        }

        $post->bookingLink = $fields['booking_link'] ?? false;
        $post->placeInfo = self::createPlaceInfoList($fields);
        $post->pin = self::createPin($post, $directions);

        return $post;
    }

    private function createPin($post, $directions = false)
    {
        return \Municipio\Helper\Location::createPin($post, $directions);
    }

    private function createPlaceInfoList($fields)
    {
        // Phone number
        $list = [];
        if (!empty($fields['phone'])) {
            $list['phone'] = ListingHelper::createListingItem(
                $fields['phone'],
                '',
                ['src' => 'call']
            );
        }

        // Website link (with fixed label)
        if (!empty($fields['website'])) {
            $list['website'] = ListingHelper::createListingItem(
                __('Visit website', 'municipio'),
                $fields['website'],
                ['src' => 'language'],
            );
        }

        // Apply filters to listing items
        $list = apply_filters(
            'Municipio/Controller/SingularPurpose/listing',
            $list,
            $fields
        );

        return $list;
    }

    private function createExcerpt($post)
    {
        if ($post->postContent) {
            return wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->postContent)), 10, '...');
        }
        return false;
    }
}
