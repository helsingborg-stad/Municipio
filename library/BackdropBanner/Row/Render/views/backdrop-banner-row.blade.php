@slider__item([
    'layout' => 'center',
    'attributeList' => [
        'data-js-backdrop-banner-row' => $id,
        'style' => 'max-height: unset;'
    ]
])
    {!! $content ?? '' !!}
@endslider__item