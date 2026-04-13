@if ($notice && $notice['toast'])
    @toast(['position' => 'bottom-left', 'attributeList' => ['data-scope' => 's-toast-notices;']])
        @foreach ($notice['toast'] as $noticeItem)
            @toast__item($noticeItem)
            @endtoast__item
        @endforeach
    @endtoast
@endif