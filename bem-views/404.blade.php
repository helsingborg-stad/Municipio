@extends('templates.master')

@section('content')

    @if(file_exists(MUNICIPIO_PATH . '/views/partials/404/' . $post_type . '.blade.php'))
        @include('partials.404.' . $post_type)
    @else
        @include('partials.404.default')
    @endif

@stop
{{ /* THIS IS A SAMPLE VIEW */ }}
