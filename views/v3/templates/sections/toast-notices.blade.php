@if ($notice && $notice['toast'])
    @scope(['name' => ['s-toast-notices']])
        @toast(['position' => 'bottom-left'])
            @foreach ($notice['toast'] as $noticeItem)
                @toast__item($noticeItem)
                @endtoast__item
            @endforeach
        @endtoast
    @endscope
@endif