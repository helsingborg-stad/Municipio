@extends('templates.master')

@section('layout')
    @if (is_active_sidebar('content-area'))
        @includeIf('partials.sidebar', ['id' => 'content-area'])
    @endif

    @if (is_active_sidebar('content-area-bottom'))
         @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
    @endif
@stop
