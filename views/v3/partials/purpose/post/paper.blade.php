<div class="o-container">
    @paper([
        'attributeList' => [
            'style' => !empty($featuredImage['src']) ? 'transform:translateY(calc(max(-50%, -50px)))' : 'margin-top: 32px'
        ],
        'classList' => ['u-padding--6']
    ])
        @group([
            'justifyContent'=> 'space-between',
        ])
            @typography([
                'element' => 'h1',
                'variant' => 'h1'
            ])
                {{ $post->postTitleFiltered }}
            @endtypography
            @if ($post->callToActionItems['floating'])
                @icon($post->callToActionItems['floating'])
                @endicon
            @endif
        @endgroup
        <div class="o-grid">
            <div class="o-grid-12@sm o-grid-9@md o-grid-9@lg">
                @typography([])
                    {!! $post->postContentFiltered !!}
                @endtypography
            </div>
            <div class="o-grid-12@sm o-grid-3@md o-grid-3@lg">

                @if (!empty($post->placeInfo))
                    @listing([
                        'list' => $post->placeInfo,
                        'icon' => false,
                        'classList' => [
                            'unlist',
                            'u-padding__top--2@xs',
                            'u-padding__top--2@sm',
                            'u-padding__bottom--3',
                            'u-margin__top--2'
                        ],
                        'padding' => 4
                    ])
                    @endlisting
                @endif

                @if (!empty($post->bookingLink))
                    @button([
                        'text' => $lang->bookHere,
                        'color' => 'primary',
                        'style' => 'filled',
                        'href' => $post->bookingLink,
                        'classList' => ['u-width--100'],
                    ])
                    @endbutton
                @endif

            </div>
        </div>
    @endpaper
</div>
