@paper(['padding' => 4])
    @typography(['element' => 'h2', 'variant' => 'h5', 'classList' => ['u-margin__bottom--2']])
        {!! $lang->contactPointsLabel !!}
    @endtypography
    @foreach($contactPoints['items'] as $item)
        @button([
            'text' => $item['name'],
            'color' => 'primary',
            'href' => $item['url'],
            'icon' => $item['icon'],
            'reversePositions' => 'true',
            'classList' => ['u-margin__right--1'],
        ])
        @endbutton
    @endforeach
@endpaper
