<li class="c-nav__item">
    <button {!! $navItem['attributes'] !!} type="button" aria-label="{{$navItem['text']}}">
           <span class="hamburger-box">
            <span class="hamburger-inner"></span>
            </span>
            @if (!isset($navItem['hide_text']) || !$navItem['hide_text'])
                <span class="hamburger-label">{{$navItem['text']}}</span>
            @endif
    </button>
</li>
