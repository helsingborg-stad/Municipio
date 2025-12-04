@if (!$hideTitle && !empty($postTitle))
    @typography([
        'id'        => 'mod-modal-' . $ID . '-label',
        'element'   => 'h2', 
        'variant'   => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif

@button([
    'href'             => '',
    'text'             => $buttonText,
    'icon'             => $buttonIcon,
    'size'             => $buttonSize,
    'color'            => $buttonColor,
    'style'            => $buttonStyle,
    'reversePositions' => $reversePosition,
    'classList'        => ['open-modal'],
    'attributeList'    => ['data-open' => 'modal-' . $modalId],
])
@endbutton
@modal([
    'isPanel'      => $modalIsPanel,
    'size'         => $modalSize,
    'padding'      => $modalPadding,
    'borderRadius' => $modalBorderRadius,
    'heading'      => $useModalContentTitle ? $modalContentTitle : false,
    'id'           => 'modal-' . $modalId
])
    {!! $modalContent !!}
@endmodal
