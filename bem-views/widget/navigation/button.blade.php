<span class="c-nav__item">
    <button {!! $navItem['attributes'] !!} type="button" aria-label="{{$navItem['text']}}">
        @section('content')
            @if (isset($navItem['hide_text']) && $navItem['hide_text'])
                <span class="hidden">
                    {{$navItem['text']}}
                </span>
            @else
                {{$navItem['text']}}
            @endif
        @show
    </button>
</span>
