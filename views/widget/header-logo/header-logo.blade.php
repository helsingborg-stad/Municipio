@extends('widget.header-widget')
@section('widget')
    <a class="c-brand" href="{{$home}}" title="{{ $language['logoLabel'] }}" data-tooltip="{{ $language['logoLabel'] }}" style="display: block; max-width: {{ $maxWidth }}px;">
        {!! $logotype !!}
    </a>
@stop
