@collection__item([
    'link' => $post->getPermalink(),
    'containerAware' => true,
    'bordered' => true,
    'attributeList' => array_merge(['data-js-posts-list-item' => true], $getViewTransitionAttributes($post->getId()))
])
    @if ($appearanceConfig->shouldDisplayFeaturedImage() && !empty($post->getImage()))
        @slot('before')
            @if($post->getImage()) 
                @image(['src' => $post->getImage(), 'classList' => ['c-view-transition__image']])@endimage
            @endif
        @endslot
    @endif
    @group([ 'direction' => 'vertical' ])
        @group([ 'justifyContent' => 'space-between' ])
            @typography([ 'element' => 'h2', 'variant' => 'h3', 'classList' => ['c-view-transition__title'] ])
                {!! $post->getTitle() !!}
            @endtypography
        @endgroup
        @tags([
            'tags' => $getTags($post),
            'classList' => ['u-padding__y--2'],
            'format' => false
        ])
        @endtags
        @typography([])
            {{ $getExcerpt($post, 10) }}
        @endtypography
    @endgroup
@endcollection__item
