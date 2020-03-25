
@buttonGroup(['attributeList' => ['js-split' => '']])
    @button(['text' => $buttonText])
    @endbutton
    @dropdown([
        'items' => $items,
        'direction' => $dropdownDirection,
        'popup' => 'click',
        'itemElement' => 'div'
    ])
        @button([
            'style' => 'filled',
            'icon' => $icon,
            'size' => 'md'
        ])
        @endbutton
    @enddropdown
@endbuttonGroup
