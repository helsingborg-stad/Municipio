@extends('widget.header-widget')
@section('widget')
    <div class="c-header__logo {{$themeClass}}" data-tooltip="{{ $language['logoLabel'] }}">
        <style scoped><!-- Logotype settings -->
            a {
                max-width: {{ $maxWidth }}px;
            }
        </style>
        <a href="{{$home}}" title="{{ $language['logoLabel'] }}">
            {!! $logotype !!}
        </a>
    </div>
@stop
