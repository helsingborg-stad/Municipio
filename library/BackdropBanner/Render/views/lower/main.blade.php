@slider([
    'classList' => ['o-container'],
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