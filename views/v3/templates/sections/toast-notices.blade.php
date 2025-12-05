@if ($notice && $notice['toast'])
    @toast(['position' => 'bottom-left'])
        @foreach ($notice['toast'] as $noticeItem)
            @toast__item($noticeItem)
            @endtoast__item
        @endforeach
    @endtoast
@endif