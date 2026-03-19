@element([
    'classList' => ['backdrop-banner'],
    'attributeList' => [
        'data-js-backdrop-banner' => 'true'
    ]
])
    @element([
        'classList' => ['backdrop-banner__top', 'u-position--relative'],
        'attributeList' => [
            'data-js-backdrop-banner-top' => 'true',
        ]
    ])
        @element([
            'classList' => ['backdrop-banner__image-front'],
            'attributeList' => [
                'data-js-backdrop-banner-image-front' => 'true',
                'style' => '--backdrop-banner-image: url(' . ($startImage ?? '#') . ')',
                'data-js-start-image' => $startImage ?? '0',
            ]
        ])
            <!-- Image -->
        @endelement
        @element([
            'classList' => ['backdrop-banner__image-back'],
            'attributeList' => [
                'data-js-backdrop-banner-image-back' => 'true',
                'style' => '--backdrop-banner-image: url(' . ($startImage ?? '#') . ')',
            ]
        ])
            <!-- Image -->
        @endelement
        @includeWhen(!empty($rows), 'upper.navigation')
    @endelement
    @includeWhen(!empty($content), 'lower.main')
@endelement