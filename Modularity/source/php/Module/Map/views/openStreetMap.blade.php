@if (!$hideTitle && !empty($postTitle))
    @typography([
        'element' => 'h2', 
        'variant' => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif

@openStreetMap([
    'startPosition' => $startPosition,
    'pins' => $pins,
    'height' => $height . 'px'
])
@endopenStreetMap