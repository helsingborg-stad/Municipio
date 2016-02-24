@extends('templates.master')

@section('content')

    @if (get_field('use_google_search', 'option') === true)
        @include('partials.search.google')
    @else
        @include('partials.search.wp')
    @endif

@stop
