 @card([
    'heading' => $lang->placeTitle,
    'content' => $place['address']
])
    @if(!empty($place['lat']) && !empty($place['lng']))
        @slot('beforeContent')
            @map([
                'height' => '250px',
                'markers' => [
                    [
                        'lat' => $place['lat'],
                        'lng' => $place['lng'],
                        'content' => render_blade_view('partials.schema.event.place-marker-content', ['post' => $post])
                    ]
                ],
                'lat' => $place['lat'],
                'lng' => $place['lng'],
                'zoom' => 15
            ])
            @endmap
        @endslot
    @endif
    @if(!empty($onlineAttendance))
        @slot('aboveContent')
            @typography(['variant' => 'p', 'classList' => ['u-margin__top--0']])
                {{ $onlineAttendance['notice'] }}
            @endtypography
            @if(!empty($onlineAttendance['button']))
                @button([
                    'href' => $onlineAttendance['button']['href'],
                    'color' => 'primary',
                    'style' => 'filled',
                    'size' => 'md',
                    'icon' => 'open_in_new',
                    'fullWidth' => false,
                    'text' => $onlineAttendance['button']['text'],
                    'ariaLabel' => $onlineAttendance['button']['ariaLabel'],
                    'attributeList' => [
                        'style' => 'justify-self: start;',
                        'rel' => 'noopener',
                    ],
                    'target' => '_blank'
                ])
                @endbutton
            @endif
        @endslot
    @endif
    @if(!empty($place['url']))
        @slot('belowContent')
            @link(['href' => $place['url']])
                {{$lang->directionsLabel}}
            @endlink
        @endslot
    @endif
@endcard
