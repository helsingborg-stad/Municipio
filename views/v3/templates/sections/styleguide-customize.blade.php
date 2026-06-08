@if (isset($styleguideCustomizeMarkup) && !empty($styleguideCustomizeMarkup))
    @fab([
            'position' => 'bottom-right',
            'button' => [
                'icon' => 'tune',
                'size' => 'md',
                'color' => 'primary',
                'classList' => ['u-margin--0'],
                'ariaLabel' => $floatingMenuLabelsCustomize->buttonLabel,
                'text' => $floatingMenuLabelsCustomize->buttonLabel,
                'reversePositions' => true,
            ],
            'classList' => ['c-fab--width-xl', 'c-fab--padding-none'],
            'attributeList' => [
                'data-customizable' => 'false'
            ]
        ])
        {!! $styleguideCustomizeMarkup !!}
    @endfab
@endif