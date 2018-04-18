@if (isset($menu) && is_array($menu) && !empty($menu))
@extends('widget.header-widget')
    @section('widget')
        <nav>
            <ul class="c-nav c-nav {{$themeClass}}">
                @foreach ($menu as $link)
                    <li class="c-nav__item @if($link->object_id == $currentId) is-current @endif @if(in_array($link->object_id, $currentAncestorId)) is-current-ancestor @endif">
                        <a href="{{$link->url}}">{{$link->title}}</a>
                        @if(isset($link->children) && is_array($link->children) && !empty($link->children))
                            <ul class="c-nav__sub c-nav__sub--level-0">
                                @foreach ($link->children as $child)
                                    <li class="c-nav_item c-nav_item--sub @if($child->object_id == $currentId) is-current @endif">
                                        <a href="{{$child->url}}">{{$child->title}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    @stop
@endif
