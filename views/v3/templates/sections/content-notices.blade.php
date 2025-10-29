@if ($notice && $notice['content'])
    @element([
        'classList' => $classes ?? [
            'o-container',
            'u-margin__top--4'
        ]
    ])
            @foreach ($notice['content'] as $noticeItem)
                @notice($noticeItem)
                @endnotice
            @endforeach
    @endelement
@endif