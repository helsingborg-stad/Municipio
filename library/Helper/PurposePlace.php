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

        $fields = get_fields($post->id);

        $post->bookingLink = $fields['booking_link'] ?? false;
        $post->placeInfo = self::createPlaceInfoList($fields);

        return $post;
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
}
