@extends('widget.header-widget')
@section('widget')
    <div class="c-header__logo {{$themeClass}}" data-tooltip="{{ $language['logoLabel'] }}">
        <a href="{{$home}}" title="{{ $language['logoLabel'] }}">
            {!! $logotype !!}
        </a>
    </div>
@stop
