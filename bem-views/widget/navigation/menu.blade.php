@foreach ($navItem['menu'] as $link)
    <span {!! $link->attributes !!}>
        <a class="c-nav__action" href="{{$link->url}}">{{$link->title}}</a>
        @if (isset($link->children) && is_array($link->children) && !empty($link->children))
            <ul class="c-nav__sub">
                @foreach ($link->children as $child)
                    <li {!! $child->attributes !!}>
                        <a class="c-nav__action" href="{{$child->url}}">{{$child->title}}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </span>
@endforeach
