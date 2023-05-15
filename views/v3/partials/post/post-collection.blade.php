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
                @if (!empty($post->callToActionItems['floating']))
                    @slot('floating')
                        @icon($post->callToActionItems['floating'])
                        @endicon
                    @endslot
                @endif
                @if ($displayFeaturedImage && !empty($post->thumbnail['src']))
                    @slot('before')
                        @image([
                            'src' => $post->thumbnail['src'],
                            'alt' => $post->thumbnail['alt'],
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
                            {{ $post->postTitle }}
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
                        {{ $post->excerptShorter }}
                    @endtypography
                @endgroup
            @endcollection__item
        @endforeach
    @endcollection
@endif
