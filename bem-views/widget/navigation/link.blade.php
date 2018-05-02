<li class="c-nav__item">
    <a {!! $navItem['attributes'] !!} href="{{$navItem['url']}}" aria-label="{{$navItem['text']}}">
        @if (isset($navItem['hide_text']) && $navItem['hide_text'])
            <span class="hidden">
                {{$navItem['text']}}
            </span>
        @else
            {{$navItem['text']}}
        @endif
    </a>
</li>
