@notice([
    'type' => 'info',
    'icon' => [
        'name' => 'report',
        'size' => 'md',
        'color' => 'primary'
    ],
    'attributeList' => [
        'id' => $anchor ?? '',
    ],
])
    {!! '<strong>' . $blockTitle . '</strong>'. ': ' . $message !!}
@endnotice
