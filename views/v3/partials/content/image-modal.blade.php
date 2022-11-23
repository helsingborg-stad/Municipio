@modal([
    'heading'=> $caption,
    'isPanel' => true,
    'id' => $id,
    'overlay' => 'dark',
    'animation' => 'scale-up',
    'transparent' => true,
])

    @image([
        'src'=> $src,
        'alt' => $alt,
        'imgAttributeList' => [
            'width' => $imgAttributeList['width'],
            'height' => $imgAttributeList['height'],
            'srcset' => $imgAttributeList['srcset'],
        ]
    ])
    @endimage

@endmodal

