@group([
    'classList' => ['c-openstreetmap__post'],
    'containerAware' => true
])
    @icon([
        'icon' => 'arrow_back',
        'size' => 'md',
        'color' => 'white',
        'classList' => ['c-openstreetmap__post-icon']
    ])
    @endicon
    @if (!empty($post->thumbnail['src']))
        @hero([
            'image' => $post->thumbnail['src']
        ])
        @endhero
    @endif
    <div class="u-margin__x--2">
        @paper([
            'attributeList' => [
                'style' => !empty($post->thumbnail['src']) ? 'transform:translateY(calc(max(-50%, -50px)))' : 'margin-top: 32px'
            ],
            'containerAware' => true,
            'classList' => ['u-padding--6', 'o-container']
        ])
            @typography([
                'element' => 'h1',
                'variant' => 'h1'
            ])
                {{ $post->postTitleFiltered }}
            @endtypography
            <div class="o-grid c-openstreetmap__post-container">
                <div class="c-openstreetmap__post-content">
                    @typography([])
                        {!! $post->postContentFiltered !!}
                    @endtypography
                </div>
            </div>
        @endpaper
    </div>
@endgroup
