@if (is_array($links) && !empty($links))
@extends('widget.header-widget')
    @section('widget')
        <ul class="c-navbar t-navbar c-navbar--widget-links">
            @foreach ($links as $link)
                <li class="c-navbar__item {{$link['classes']}}">
                    @if (isset($link['url']))
                        <a href="{{$link['url']}}">{{$link['text']}}</a>
                    @else
                        <a>{{$link['text']}}</a>
                    @endif
                </li>
            @endforeach
        </ul>
    @stop
@endif
