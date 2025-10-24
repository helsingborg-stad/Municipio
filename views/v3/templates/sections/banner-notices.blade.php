{{-- Notices as banner style --}}
@if ($notice && $notice['banner'])
@element([
    'classList' => $classes ?? []
])
    @foreach ($notice['banner'] as $noticeItem)
        @notice($noticeItem)
        @endnotice
    @endforeach
@endelement
@endif