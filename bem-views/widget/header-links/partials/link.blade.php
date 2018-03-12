<a class="{{$link['classes']}}" href="{{$link['url']}}" aria-label="{{$link['text']}}" {!! $link['attributes'] !!}>

        @if (isset($link['hide_text']) && $link['hide_text'])
            <span class="hidden">
                {{$link['text']}}
            </span>
        @else
            {{$link['text']}}
        @endif

</a>
