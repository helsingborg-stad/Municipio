@paper(['classList' => ['u-padding--2']])
    @typography(['element' => 'h2', 'classList' => ['u-margin__bottom--2']])
        {!! $lang->contactPointsLabel !!}
    @endtypography
    @foreach($contactPoints['items'] as $item)
        @button([
            'text' => $item['name'],
            'color' => 'primary',
            'size' => 'lg',
            'href' => $item['url'],
            'icon' => $item['icon'],
            'reversePositions' => 'true',
            'classList' => ['u-margin__right--1'],
        ])
        @endbutton
    @endforeach
@endpaper
