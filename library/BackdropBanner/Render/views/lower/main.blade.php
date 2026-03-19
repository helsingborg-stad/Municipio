@slider([
    'classList' => ['o-container', 'o-container--wide', 'backdrop-banner__bottom'],
    'attributeList' => [
        'data-js-backdrop-banner-slider' => 'true',
    ],
    'type' => 'fade',
    'repeatSlide' => false,
    'showStepper' => false,
    'navigationHover' => false,
    'autoSlide' => false
])
    {!! $content !!}
@endslider