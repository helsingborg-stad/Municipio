@extends('widget.header-widget')
@section('widget')
    <style scoped>
        .c-header__logo.t-municipio a img,
        .c-header__logo.t-municipio a svg {
            max-width: {{ $maxWidth }}px;
        }
    </style>
    <div class="c-header__logo {{$themeClass}}" data-tooltip="{{ $language['logoLabel'] }}">
        <a href="{{$home}}" title="{{ $language['logoLabel'] }}">
            {!! $logotype !!}
        </a>
    </div>
@stop
