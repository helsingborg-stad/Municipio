@slider([
    'classList' => ['o-container', 'o-container--wide', 'backdrop-banner__bottom', 'u-margin__top--10'],
    'attributeList' => [
        'data-js-backdrop-banner-slider' => 'true',
    ],
    'type' => 'fade',
    'repeatSlide' => false,
    'showStepper' => false,
    'navigationHover' => false,
    'autoSlide' => false,
    'ratio' => 'unset'
])
    {!! $content !!}
@endslider