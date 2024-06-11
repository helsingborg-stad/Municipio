<?php

namespace Municipio\PostDecorators;

use AcfService\Contracts\GetFields;
use Municipio\Helper\Listing;
use WP_Post;

class ApplyInfoListToPlace implements PostDecorator
{
    public function __construct(
        private GetFields $acfService,
        private Listing $listingHelper,
        private ?PostDecorator $inner = new NullDecorator()
    ) {
    }

    public function apply(WP_Post $post): WP_Post
    {
        $post = $this->inner->apply($post);

        if (empty($post->schemaObject) || $post->schemaObject->getType() !== 'Place') {
            return $post;
        }

        $fields = $this->acfService->getFields($post->ID ?? null);
        $list   = [];

        // Phone number
        if (!empty($fields['phone'])) {
            $list['phone'] = $this->listingHelper::createListingItem($fields['phone'], '', ['src' => 'call']);
        }

        if (!empty($fields['website'])) {
            $list['website'] = $this->listingHelper::createListingItem(__('Visit website', 'municipio'), $fields['website'], ['src' => 'language']);
        }

        $post->placeInfo = apply_filters('Municipio/Controller/SingularContentType/listing', $list, $fields);

        return $post;
    }
}
