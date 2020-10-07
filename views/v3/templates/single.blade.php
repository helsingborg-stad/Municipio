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

    @sidebar([
        'items'     => $secondaryMenuItems,
        'endpoints' => [
            'children'  => $homeUrlPath . '/wp-json/municipio/v1/navigation/children'
        ],
        'classList' => [
            'u-visibility--hidden@xs',
            'u-visibility--hidden@sm',
        ],
        'pageId' => $pageID,
        'sidebar' => true
    ])
    @endsidebar

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

@stop

@section('sidebar-right')
    @includeIf('partials.sidebar', ['id' => 'right-sidebar', 'classes' => ['o-grid']])
@stop

@section('below')
    @includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])
@stop