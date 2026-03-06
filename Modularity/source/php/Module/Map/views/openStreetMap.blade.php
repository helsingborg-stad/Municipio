@if (!$hideTitle && !empty($postTitle))
    @typography([
        'element' => 'h2', 
        'variant' => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif



@map([
    'markers' => $markers,
    'height' => $height . 'px',
    'lat' => $lat,
    'lng' => $lng,
    'zoom' => $zoom,
])
@endmap