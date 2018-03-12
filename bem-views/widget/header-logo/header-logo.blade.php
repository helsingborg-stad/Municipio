@extends('widget.header-widget')
@section('widget')
    <style scoped>
        .c-header__logo.t-municipio a img,
        .c-header__logo.t-municipio a svg {
            max-width: {{ $maxWidth }}px;
        }
        @if($imageRatio)
            .c-header__logo.t-municipio a {
                position: relative;
                padding-top: {{ $imageRatio }}%;
                max-width: 100%;
                width: {{ $maxWidth }}px;
            }
            .c-header__logo.t-municipio a svg {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }
            @media (max-width: 599px) {
                .c-header__logo.t-municipio a {
                    width: {{ ($maxWidth/2) }}px;
                }
            }
            @media (min-width: 600px) and (max-width: 799px) {
                .c-header__logo.t-municipio a {
                    width: {{ ($maxWidth/3)*2 }}px;
                }
            }
        @endif
    </style>
    <div class="c-header__logo {{$themeClass}}" data-tooltip="{{ $language['logoLabel'] }}">
        <a href="{{$home}}" title="{{ $language['logoLabel'] }}">
            {!! $logotype !!}
        </a>
    </div>
@stop
