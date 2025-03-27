@if ($posts)
    @collection([
        'unbox' => true,
        'classList' => ['o-grid', 'o-grid--horizontal']
    ])
        @foreach ($posts as $post)
            @collection__item([
                'link' => $post->permalink,
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
                @if ($displayFeaturedImage && !empty($post->images['thumbnail16:9']['src']))
                    @slot('before')
                    @if($post->imageContract) 
                        @image([
                            'src' => $post->imageContract
                        ])
                        @endimage
                    @else 
                        @image([
                            'src' => $post->images['thumbnail16:9']['src'],
                            'alt' => $post->images['thumbnail16:9']['alt'],
                            'placeholderIconSize' => 'sm',
                            'placeholderIcon' => 'image',
                            'placeholderText' => ''
                        ])
                        @endimage
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
                            {!! $post->postTitle !!}
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
                        {{ \Municipio\Helper\Sanitize::sanitizeATags($post->excerptShorter) }}
                    @endtypography
                @endgroup
            @endcollection__item
        @endforeach
    @endcollection
@endif
