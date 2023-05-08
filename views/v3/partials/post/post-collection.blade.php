@if ($posts)
    @collection([
        'unbox' => true,
        'classList' => ['c-collection--posts', 'o-grid', 'o-grid--horizontal']
    ])
        @foreach ($posts as $post)
                @collection__item([
                    'link' => $post->permalink,
                    'classList' => ['c-collection__item--post', $gridColumnClass],
                    'containerAware' => true,
                    'bordered' => true,
                ])
                @if ($displayFeaturedImage && !empty($post->thumbnail['src']))
                    @slot('before')
                        @image([
                            'src' => $post->thumbnail['src'],
                            'alt' => $post->thumbnail['alt'],
                            'placeholderIconSize' => 'sm',
                            'placeholderIcon' => 'image',
                            'placeholderText' => '',
                            'classList' => ['u-width--100'],
                        ])
                        @endimage
                    @endslot
                @endif

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
                @endcollection__item
        @endforeach
    @endcollection
@endif
