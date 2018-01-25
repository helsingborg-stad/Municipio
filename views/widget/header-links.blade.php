@if (is_array($links) && !empty($links))
@extends('widget.header-widget')
    @section('widget')
        <ul class="c-navbar c-navbar--header-widget-links {{$themeClass}}">
            @foreach ($links as $link)
                <li class="c-navbar__item">
                    @if (isset($link['url']))
                        <a class="{{$link['classes']}}" {!!$link['attributes']!!} href="{{$link['url']}}">
                            @if (isset($link['hide_text']) && $link['hide_text'])<span class="hidden">@endif
                                {{$link['text']}}
                            @if (isset($link['hide_text']) && $link['hide_text'])</span>@endif
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @stop
@endif
