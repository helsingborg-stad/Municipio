@button([
    'text' => $lang->bookHere ?? 'Book here',
    'color' => 'primary',
    'style' => 'filled',
    'href' => $post->bookingLink,
    'classList' => ['u-width--100'],
])
@endbutton