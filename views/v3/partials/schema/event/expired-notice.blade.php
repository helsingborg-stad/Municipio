@if($eventIsInThePast)
    @element([
        'classList' => $classes ?? []
    ])
        @notice([
            'type' => 'warning',
            'message' => [
                'text' => $lang->expiredEventNotice,
            ],
            'icon' => [
                'icon' => 'schedule'
            ]
        ])@endnotice
    @endelement
@endif