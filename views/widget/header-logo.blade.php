@extends('widget.header-widget')
    @section('widget')
        <div class="c-header__logo {{$themeClass}}">
            <a href="{{$home}}">
                {!! $logotype !!}
            </a>
        </div>
    @stop

