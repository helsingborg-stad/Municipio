@element([
    'classList' => [
        'o-layout-grid',
        'o-layout-grid--cq',
        'backdrop-banner__navigation',
        'o-container',
    ],
    'attributeList' => [
        'data-js-backdrop-banner-navigation' => 'true'
    ]
])
    @element([
        'classList' => ['backdrop-banner__navigation-container'],
    ])
        @foreach($rows as $index => $row)
            @include('upper.items', ['row' => $row, 'index' => $index])
        @endforeach
    @endelement
@endelement