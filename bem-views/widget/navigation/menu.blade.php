@foreach ($navItem['menu'] as $link)
    <span {!! $link->attributes !!}>
        <a class="c-navbar__action" href="{{$link->url}}">{{$link->title}}</a>
        @if (isset($link->children) && is_array($link->children) && !empty($link->children))
            <ul class="c-navbar__sub">
                @foreach ($link->children as $child)
                    <li {!! $child->attributes !!}>
                        <a class="c-navbar__action" href="{{$child->url}}">{{$child->title}}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </span>
@endforeach
