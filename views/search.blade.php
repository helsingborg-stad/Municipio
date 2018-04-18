@extends('templates.master')

@section('content')
    @switch($activeSearchEngine)
        @case("google")
            @include('partials.search.google')
            @break
        @case("algoliacustom")
            @include('partials.search.algolia-customsearch')
            @break
        @case("algolia")
            @include('partials.search.algolia')
            @break
        @default
            @include('partials.search.wp')
    @endswitch
@stop
