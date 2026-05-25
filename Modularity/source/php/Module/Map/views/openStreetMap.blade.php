@card()
    @if (!$hideTitle && !empty($postTitle))
        @if (!$hideTitle && !empty($postTitle))
            @element(['classList' => ['c-card__header']])       
                @typography([
                    'element' => 'h2',
                    'variant' => 'h2',
                ])
                    {!! $postTitle !!}
                @endtypography
            @endelement
        @endif
    @endif
    @map([
        'markers' => $markers,
        'height' => $height . 'px',
        'lat' => $lat,
        'lng' => $lng,
        'zoom' => $zoom,
    ])
    @endmap
@endcard