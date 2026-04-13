@if (isset($styleguideCustomizeMarkup) && !empty($styleguideCustomizeMarkup))
    @fab([
            'position' => 'bottom-right',
            'button' => [
                'icon' => 'tune',
                'size' => 'md',
                'color' => 'primary',
                'shape' => 'pill',
                'classList' => ['u-margin--0'],
                'ariaLabel' => 'Open component customizer'
            ],
            'classList' => ['c-fab--width-xl'],
            'attributeList' => [
                'data-customizable' => 'false'
            ]
        ])
        {!! $styleguideCustomizeMarkup !!}
    @endfab
@endif