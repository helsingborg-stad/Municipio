@foreach ($navItem['menu'] as $link)
    <li {!! $link->attributes !!}>
        <a class="c-nav__link" href="{{$link->url}}">{{$link->title}}</a>
        @if (isset($link->children) && is_array($link->children) && !empty($link->children))
            <ul class="c-nav__sub">
                @foreach ($link->children as $child)
                    <li {!! $child->attributes !!}>
                        <a class="c-nav__link" href="{{$child->url}}">{{$child->title}}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
