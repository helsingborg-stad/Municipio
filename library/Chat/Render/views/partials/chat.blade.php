@chat(array_merge($chatConfig, [
    'persistent' => true,
    'chatInputData' => [
        'sendButtonText' => $lang['send'],
        'placeholderText' => $lang['placeholder']
    ]])
)
    @if (!empty($header))
        @slot('titleArea')
            @include('partials.header')
        @endslot
    @endif
    @slot('belowChatArea')
        @include('partials.template')
        @include('partials.feedback')
    @endslot
@endchat