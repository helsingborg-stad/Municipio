@slider__item([
    'layout' => 'center',
    'attributeList' => [
        'data-js-backdrop-banner-row' => $id
    ]
])
    {!! $content ?? '' !!}
@endslider__item