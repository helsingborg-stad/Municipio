@extends('templates.master')

@section('before-layout')
@stop

@section('helper-navigation')
    @includeIf('partials.navigation.helper')
@stop

@section('above')
    @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => ['o-grid']])
@stop

@section('sidebar-left')
    @if($showSidebars)

        @if($customize->general->secondaryNavigationPosition == 'left') 
            @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenuItems])
        @endif

        @include('partials.sidebar', ['id' => 'left-sidebar', 'classes' => ['o-grid']])
        @include('partials.sidebar', ['id' => 'left-sidebar-bottom', 'classes' => ['o-grid']])
    @endif
@stop

@section('content')

    {!! $hook->loopStart !!}

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    @section('loop')
        @includeIf('partials.loop')
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
    @if($showSidebars)

        @if($customize->general->secondaryNavigationPosition == 'right') 
            @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenuItems])
        @endif

        @includeIf('partials.sidebar', ['id' => 'right-sidebar', 'classes' => ['o-grid']])
    @endif
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