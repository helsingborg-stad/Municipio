<?php $classes = (isset($classes)) ? $classes : ""; ?>

@if(isset($links) && is_array($links) && !empty($links))
    <ul class="c-navbar t-navbar {{$classes}}">
        @foreach ($links as $link)
            <li class="c-navbar__item">
                <a href="{{$link->url}}">{{$link->title}}</a>
                @if(isset($link->children) && is_array($link->children) && !empty($link->children))
                    <ul class="c-navbar__sub">
                        @foreach ($link->children as $child)
                            <li class="c-navbar__sub_item">
                                <a href="{{$child->url}}">{{$child->title}}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
@endif
