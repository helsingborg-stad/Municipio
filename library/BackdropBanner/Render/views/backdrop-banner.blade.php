@element([
    'classList' => ['backdrop-banner'],
])
    @element([
        'classList' => ['backdrop-banner__top', 'u-position--relative'],
        'attributeList' => [
            'style' => 'background-image: url(' . $startImage . '); min-height: min(640px, 70vh);'
        ]
    ])
        @includeWhen(!empty($rows), 'upper.navigation')
    @endelement
@endelement