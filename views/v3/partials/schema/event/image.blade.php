@if($post->getImage())
    @image([
        'src' => $post->getImage(),
        'rounded' => 'lg',
        'classList' => [
            'u-width--100'
        ]
    ])
    @endimage
@endif