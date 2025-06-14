@if ($contact['other'])
    @typography([
        'element' => 'div',
        'variant' => 'meta',
        'classList' => [
            'u-margin__x--2',
            'u-margin__bottom--1',
            'u-color__text--darker'
        ]
    ])
        {!! $contact['other']  !!}
    @endtypography
@endif