@extends('widget.header-widget')
@section('widget')
    <style scoped>
        .c-header__logo.t-municipio a img {
            max-width: {{ $maxWidth }}px;
        }
        .c-header__logo.t-municipio a svg {
            @media screen and (max-width: 599px) {
                max-width: {{ $maxWidth }}px;
            }
            @media screen and (min-width: 600px) {
                width: {{ $maxWidth }}px;
            }
        }
    </style>
    <div class="c-header__logo {{$themeClass}}" data-tooltip="{{ $language['logoLabel'] }}">
        <a href="{{$home}}" title="{{ $language['logoLabel'] }}">
            {!! $logotype !!}
        </a>
    </div>
@stop
