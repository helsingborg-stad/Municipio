@modal([
    'heading'=> $caption,
    'isPanel' => false,
    'id' => $modalId,
    'overlay' => 'dark',
    'animation' => 'scale-up'
])
    @image([
        'src'=> $src,
        'alt' => $caption
    ])
    @endimage

@endmodal