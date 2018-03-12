@if (isset($menu) && is_array($menu) && !empty($menu))
@extends('widget.header-widget')
    @section('widget')
        <nav>
            <ul class="c-navbar c-navbar--header-widget-menu {{$themeClass}}">
                @foreach ($menu as $link)
                    <li class="c-navbar__item @if($link->object_id == $currentId) is-current @endif @if(in_array($link->object_id, $currentAncestorId)) is-current-ancestor @endif">
                        <a href="{{$link->url}}">{{$link->title}}</a>
                        @if(isset($link->children) && is_array($link->children) && !empty($link->children))
                            <ul class="c-navbar__sub c-navbar__sub-level-0">
                                @foreach ($link->children as $child)
                                    <li class="c-navbar__sub_item @if($child->object_id == $currentId) is-current @endif">
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
