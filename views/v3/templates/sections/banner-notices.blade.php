{{-- Notices as banner style --}}
@if ($notice && $notice['banner'])
    @foreach ($notice['banner'] as $noticeItem)
        @notice($noticeItem)
        @endnotice
    @endforeach
@endif