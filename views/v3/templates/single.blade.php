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
    @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenuItems])

    @include('partials.sidebar', ['id' => 'left-sidebar', 'classes' => ['o-grid']])
    @include('partials.sidebar', ['id' => 'left-sidebar-bottom', 'classes' => ['o-grid']])
@stop

@section('content')

    {!! $hook->loopStart !!}

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    @section('loop')
        {!! $hook->innerLoopStart !!}
        @if($post)
            @include('partials.article', (array) $post)
        @endif
        {!! $hook->innerLoopEnd !!}
    @show

    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

    {!! $hook->loopEnd !!}

    @include(
        'partials.signature',
        [
            'classList' => [
                'u-margin__y--2',
                'u-display--none@xs',
                'u-display--none@sm'
            ]
        ]
    )

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar', 'classes' => ['o-grid']])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])

    @include(
        'partials.signature', 
        [
            'classList' => [
                'u-margin__y--2',
                'u-display--none@md',
                'u-display--none@lg'
            ]
        ]
    )
@stop