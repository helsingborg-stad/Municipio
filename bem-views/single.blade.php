@extends('templates.layout.three-column')

@section('sidebar-left')
    @include('components.sidebar-area', ['id' => 'left-sidebar'])
    @include('components.navigation.sidebar-menu')
    @include('components.sidebar-area', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')
    @include('components.sidebar-area', ['id' => 'content-area-top'])

    @while(have_posts())
        {!! the_post() !!}

        @include('partials.article')
    @endwhile

    @include('components.sidebar-area', ['id' => 'content-area'])
@stop

@section('sidebar-right')
    @include('components.sidebar-area', ['id' => 'right-sidebar'])
@stop
