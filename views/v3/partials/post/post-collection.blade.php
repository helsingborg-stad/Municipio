@if ($posts)
    @collection([
        'unbox' => true,
        'classList' => ['c-collection--posts', 'o-grid']
    ])
        @foreach ($posts as $post)
        <div class="{{$gridColumnClass}}">
            @collection__item([
                'link' => $post->permalink,
                'classList' => ['c-collection__item--post'],
                'containerAware' => true,
            ])
               @slot('before')
                    @if($displayFeaturedImage)
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
                    'variant' => 'h3',
                ])
                     {{ $post->postTitle }}
                @endtypography
                {{-- TODO: Add icon --}}
            @endgroup
                @tags([
                    'tags' => $post->termsUnlinked,
                    'classList' => ['u-padding__y--2'],
                    'format' => false,
                ])
                @endtags
                @typography([])
                    {{ $post->excerptShorter }}
                @endtypography
            @endgroup
        @endcollection__item
        </div>
        @endforeach
    @endcollection
@endif
