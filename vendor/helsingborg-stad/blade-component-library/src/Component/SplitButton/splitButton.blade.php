
@buttonGroup(['attributeList' => ['js-split' => ''], 'borderColor' => $borderColor])
    @button(['background' => $backgroundColor, 'text' => $buttonText])
    @endbutton
    @dropdown([
    'items' => $items,
    'direction' => $dropdownDirection,
    'popup' => 'click',
    'itemElement' => 'div'
    ])
        @button([
            'isIconButton' => true,
            'icon' => $icon,
            'size' => 'md',
            'background' => $backgroundColor
            
        ])
        @endbutton
    @enddropdown
@endbuttonGroup
