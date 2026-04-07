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
    @if(!empty($place['url']))
        @slot('belowContent')
            @link(['href' => $place['url']])
                {{$lang->directionsLabel}}
            @endlink
        @endslot
    @endif
@endcard
