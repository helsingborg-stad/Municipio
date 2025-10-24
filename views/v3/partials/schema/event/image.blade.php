@if($post->getImage())
    @image([
        'src' => $post->getImage(),
        'rounded' => 'lg',
        'classList' => [
            'u-aspect-ratio--16-9',
            'u-width--100'
        ]
    ])
    @endimage
@endif