@extends('templates.master')

@section('before-layout')
@stop

@section('above')
    <div class="nav-helper">
        @includeIf('partials.navigation.breadcrumb')
        @includeIf('partials.navigation.accessibility')
    </div>
@stop

@section('sidebar-left')
    @include('partials.sidebar', ['id' => 'left-sidebar'])
    @include('partials.sidebar', ['id' => 'right-sidebar', 'classes' => 'hidden-lg'])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')

    @includeIf('partials.sidebar', ['id' => 'content-area-top'])

    @section('loop')
        {!! $hook->loopStart !!}
        @if($post)
            @include('partials.article', (array) $post)
        @endif
        {!! $hook->loopEnd !!}
    @show

    @includeIf('partials.sidebar', ['id' => 'content-area'])

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom'])
@stop
