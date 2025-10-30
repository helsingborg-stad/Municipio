@collection__item([
    'link' => $post->getPermalink(),
    'classList' => [$gridColumnClass, 'u-level-2'],
    'containerAware' => true,
    'bordered' => true
])
    @if (!empty($callToActionItems['floating']['icon']) && !empty($callToActionItems['floating']['wrapper']))
        @element($callToActionItems['floating']['wrapper'] ?? [])
            @icon($callToActionItems['floating']['icon'])
            @endicon
        @endelement
    @endif
    @if ($config->shouldDisplayFeaturedImage() && !empty($post->getImage()))
        @slot('before')
        @if($post->getImage()) 
            @image(['src' => $post->getImage()])@endimage
        @endif
            
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
                {!! $post->getTitle() !!}
            @endtypography
            {{-- TODO: Add icon --}}
        @endgroup
        @tags([
            'tags' => $post->termsUnlinked,
            'classList' => ['u-padding__y--2'],
            'format' => false
        ])
        @endtags
        @typography([])
            {{ \Municipio\Helper\Sanitize::sanitizeATags($post->getExcerpt()) }}
        @endtypography
    @endgroup
@endcollection__item