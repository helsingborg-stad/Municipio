@slider([
    'classList' => ['o-container'],
    'attributeList' => [
        'data-js-backdrop-banner-slider' => 'true',
    ],
    'repeatSlide' => false,
    'showStepper' => false,
    'navigationHover' => false,
    'autoSlide' => false
])
    {!! $content !!}
@endslider