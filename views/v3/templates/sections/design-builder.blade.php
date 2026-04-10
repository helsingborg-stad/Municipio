@if (isset($designBuilderMarkup) && !empty($designBuilderMarkup))
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
        {!! $designBuilderMarkup !!}
    @endfab
@endif