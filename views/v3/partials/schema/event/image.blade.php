@if($post->getImage())
    @image([
        'src' => $post->getImage(),
        'rounded' => 'lg',
        'calculateAspectRatio' => false,
        'classList' => [
            'u-width--100',
            'u-aspect-ratio--16-9'
        ]
    ])
    @endimage
@endif