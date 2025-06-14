@accordion__item([
    'heading' => [$lang->opening_hours]
])
    @typography([
        "element"       => "p",
        'variant'       => 'meta',
        'classList'     => [
            'u-margin__top--0',
            'u-color__text--darker'
        ],
        'attributeList' => [
            'translate' => 'no'
        ]
    ])
    {!! $contact['opening_hours'] !!}
    @endtypography
@endaccordion__item