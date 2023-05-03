@collection__item([
    'classList' => ['c-collection__item--post', 'c-openstreetmap__collection__item'],
    'containerAware' => true,
    'attributeList' => [
        'js-map-lat' => $post->location['lat'],
        'js-map-lng' => $post->location['lng']
    ]
])
    @slot('before')
        @if (!empty($post->thumbnail['src']))
            @image([
                'src' => $post->thumbnail['src'],
                'alt' => $post->thumbnail['alt'] ? $post->thumbnail['alt'] : $post->postTitle,
                'classList' => ['u-width--100']
            ])
            @endimage
        @endif
    @endslot
    @group([
        'direction' => 'vertical'
    ])
        @group([
            'justifyContent' => 'space-between',
            'alignItems' => 'flex-start'
        ])
            @typography([
                'element' => 'h2',
                'variant' => 'h3'
            ])
                {{ $post->postTitle }}
            @endtypography
            @if ($post->termIcon['icon'])
                @inlineCssWrapper([
                    'styles' => ['background-color' => $post->termIcon['backgroundColor'], 'display' => 'flex'],
                    'classList' => [
                        $post->termIcon['backgroundColor'] ? '' : 'u-color__bg--primary',
                        'u-rounded--full',
                        'u-detail-shadow-3'
                    ]
                ])
                    @icon($post->termIcon)
                    @endicon
                @endinlineCssWrapper
            @endif
        @endgroup
        @tags([
            'tags' => $post->termsUnlinked,
            'classList' => ['u-padding__y--2'],
            'format' => true
        ])
        @endtags
        @typography([])
            {{ $post->postExcerpt }}
        @endtypography
    @endgroup
@endcollection__item
