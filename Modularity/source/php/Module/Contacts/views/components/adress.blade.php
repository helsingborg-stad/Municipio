@accordion__item([
    'heading' => [$lang->address],
    'attributeList' => ['itemprop' => 'adress'],
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
    {!! $contact['address'] !!}
    @endtypography
@endaccordion__item