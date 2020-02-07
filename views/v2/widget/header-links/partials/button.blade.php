<button class="{{$link['classes']}}" type="button" aria-label="{{$link['text']}}" {!! $link['attributes'] !!}>
    @section('content')
        @if (isset($link['hide_text']) && $link['hide_text'])
            <span class="hidden">
                {{$link['text']}}
            </span>
        @else
            {{$link['text']}}
        @endif
    @show
</button>
