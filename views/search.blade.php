@extends('templates.master')

@section('content')

    @if (get_field('use_google_search', 'option') === true)
        @include('partials.search.google')
    @elseif(get_field('use_algolia_search', 'option') === true)
        @if(function_exists('queryAlgoliaSearch'))
            @include('partials.search.algolia-customsearch')
        @else
            @include('partials.search.algolia')
        @endif
    @else
        @include('partials.search.wp')
    @endif

@stop
