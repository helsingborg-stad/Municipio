@if ($posts)
    @collection([
        'unbox' => true,
        'classList' => ['o-grid', 'o-grid--horizontal']
    ])
        @foreach ($posts as $post)
        @php echo '<pre>' . print_r( $post->callToActionItems['floating'], true ) . '</pre>'; @endphp
            @collection__item([
                'link' => $post->permalink,
                'classList' => [$gridColumnClass],
                'containerAware' => true,
                'bordered' => true,
            ])

            @if (!empty($post->callToActionItems['floating']))
                @slot('floating')
                        @icon($post->callToActionItems['floating'])
                        @endicon
                @endslot
            @endif
                @slot('before')
                    @if ($displayFeaturedImage)
                        @image([
                            'src' => $post->thumbnail['src'],
                            'alt' => $post->thumbnail['alt'],
                            'placeholderIconSize' => 'sm',
                            'placeholderIcon' => 'image',
                            'placeholderText' => '',
                        ])
                        @endimage
                    @endif
                @endslot

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
