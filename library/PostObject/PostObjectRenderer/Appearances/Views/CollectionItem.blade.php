@collection__item([
    'link' => $postObject->permalink,
    'classList' => [...$config['gridColumnClass'], ...['u-level-2']],
    'containerAware' => true,
    'bordered' => true
])
    @if (!empty($postObject->callToActionItems['floating']))
        @slot('floating')
            @icon($postObject->callToActionItems['floating'])
            @endicon
        @endslot
    @endif
    @if ($config['displayFeaturedImage'] && !empty($postObject->images['thumbnail16:9']['src']))
        @slot('before')
        @image([
                'src' => $postObject->images['thumbnail16:9']['src'],
                'alt' => $postObject->images['thumbnail16:9']['alt'],
                'placeholderIconSize' => 'sm',
                'placeholderIcon' => 'image',
                'placeholderText' => ''
            ])
            @endimage
            
        @endslot
    @endif

    @group([
        'direction' => 'vertical'
    ])
        @group([
            'justifyContent' => 'space-between'
        ])
            @typography([
                'element' => 'h2',
                'variant' => 'h3'
            ])
                {!! $postObject->postTitle !!}
            @endtypography
            {{-- TODO: Add icon --}}
        @endgroup
        @tags([
            'tags' => $postObject->termsUnlinked,
            'classList' => ['u-padding__y--2'],
            'format' => false
        ])
        @endtags
        @typography([])
            {{ $postObject->excerptShorter }}
        @endtypography
    @endgroup
@endcollection__item
