@if (!empty($preFooterImageStyle))
    @element([
        'classList' => $preFooterImageClassList,
        'hideIfNoContent' => false,
        'attributeList' => [
            'style' => $preFooterImageStyle,
            'aria-hidden' => 'true',
        ],
    ])
    @endelement
@endif
