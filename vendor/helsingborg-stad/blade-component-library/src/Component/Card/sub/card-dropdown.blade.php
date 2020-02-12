@dropdown([
    'items' => $dropdown['items'],
    'direction' => $dropdown['direction'],
    'popup' => 'click'
])
    @button([
        'icon' => 'more_vert',
        'classList' => ['u-float--right'],
        'type' => 'basic',
        'color' => 'primary'
        ])
    @endbutton
@enddropdown