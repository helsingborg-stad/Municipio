@element([
    'classList' => [
        'u-display--flex',
        'u-justify-content--end'
    ] 
])
    @button([
        'style' => 'outlined',
        'color' => 'secondary',
        'text' => $lang['logoutUrl'],
        'href' => $logoutUrl
    ])
    @endbutton
    @if(!empty($attributes['ticketLink']))
        @button([
            'style' => 'filled',
            'color' => 'primary',
            'text' => $lang['myTicket'],
            'href' => $attributes['ticketLink'] ?? ''
        ])
        @endbutton
    @endif
    @button([
        'type' => 'submit',
        'color' => 'primary',
        'text' => $lang['saveUrl'],
    ])
    @endbutton
@endelement