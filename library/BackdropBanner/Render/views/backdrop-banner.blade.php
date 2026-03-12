@element([
    'classList' => ['backdrop-banner'],
])
    @element([
        'classList' => ['backdrop-banner__top', 'u-position--relative'],
        'attributeList' => [
            'style' => 'background-image: url(' . $startImage . ');'
        ]
    ])
        @includeWhen(!empty($rows), 'upper.navigation')
    @endelement
@endelement