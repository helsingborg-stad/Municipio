<div class="notice {{ $notice['class'] }}">
    <div class="container">
        <div class="grid grid-va-middle">
            @if (isset($notice['icon']))
            <div class="grid-fit-content no-padding">
                <i class="{{ $notice['icon'] }}"></i>
            </div>
            @endif

            <div class="grid-auto">
                {!! $notice['text'] !!}
            </div>

            @if (isset($notice['buttons']) && is_array($notice['buttons']))
            <div class="grid-fit-content no-padding">
                @foreach ($notice['buttons'] as $button)
                <a href="{{ $button['url'] }}" class="{{ $button['class'] }}">{{ $button['text'] }}</a>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
