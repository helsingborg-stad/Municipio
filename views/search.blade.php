@extends('templates.master')

@section('content')

    @if (get_field('use_google_search', 'option') === true)
        @include('partials.search.google')
    @elseif(get_field('use_algolia_search', 'option') === true)
        @include('partials.search.algolia')
    @else
        @include('partials.search.wp')
    @endif

@stop
