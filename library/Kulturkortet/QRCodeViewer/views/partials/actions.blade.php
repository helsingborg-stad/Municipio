@element([
    'classList' => [
        'kulturkortet-actions'
    ]
])
    @button([
        'style' => 'outlined',
        'color' => 'primary',
        'href' => $logoutUrl,
        'text' => $lang['logout'],
        'classList' => ['u-margin__left--0'],
        'icon' => 'logout',
        'reversePositions' => true
    ])
    @endbutton

    @if(!empty($attributes['profileLink']))
        @button([
            'style' => 'filled',
            'color' => 'primary',
            'href' => $attributes['profileLink'],
            'text' => $lang['profile'],
            'icon' => 'person',
            'reversePositions' => true
        ])
        @endbutton
    @endif
@endelement