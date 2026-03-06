@element([
    'classList' => ['interactive-map', 'o-grid', ($stretch ? 'o-grid--stretch' : '')],
    'attributeList' => $attributeList
])
    @includeWhen((!empty($selectFilters) || !empty($buttonFilters)) && $allowFiltering, 'filterIcon')

    @map([
        'lat' => $startLat,
        'lng' => $startLng,
        'zoom' => $startZoom,
        'height' => $mapSize,
        'mapStyle' => $mapStyle,
        'provider' => 'openstreetmap',
        'markers' => [],
    ])
    @endmap

    @includeWhen((!empty($selectFilters) || !empty($buttonFilters)) && $allowFiltering, 'filters')
    @include('markerInfo')
@endelement
