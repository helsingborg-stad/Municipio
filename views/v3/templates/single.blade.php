@extends('templates.master')

@section('before-layout')
@stop

@section('helper-navigation')
    @includeWhen($helperNavBeforeContent, 'partials.navigation.helper')
@stop

@section('hero-top-sidebar')
    @includeIf('partials.hero')
    @includeIf('partials.sidebar', ['id' => 'top-sidebar'])
@stop

@section('above')
    @include('partials.sidebar', ['id' => 'above-columns-sidebar', 'classes' => ['o-grid']])
@stop

@section('sidebar-left')
    @if ($showSidebars)

        @include('partials.sidebar', [
            'id' => 'left-sidebar',
            'classes' => ['o-grid'],
        ])

        @if ($customizer->secondaryNavigationPosition == 'left')
            @if (!empty($secondaryMenu['items']))
                <div class="u-margin__bottom--4 u-display--none@xs u-display--none@sm u-display--none@md">
                    @paper()
                        @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenu['items']])
                    @endpaper
                </div>
            @endif
        @endif

        @include('partials.sidebar', [
            'id' => 'left-sidebar-bottom',
            'classes' => ['o-grid'],
        ])

    @endif
@stop

@section('content')

    {!! $hook->loopStart !!}

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    @section('loop')
        @includeIf('partials.loop')
    @show

    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

    @includeWhen($displayQuicklinksAfterContent, 'partials.navigation.fixed')

    {!! $hook->loopEnd !!}

@stop

@section('sidebar-right')
@if ($showSidebars)
    @if ($customizer->secondaryNavigationPosition == 'right')
        @if (!empty($secondaryMenu['items']))
            <div class="u-margin__bottom--4 u-display--none@xs u-display--none@sm u-display--none@md">
                @paper()
                    @includeIf('partials.navigation.sidebar', ['menuItems' => $secondaryMenu['items']])
                @endpaper
            </div>
        @endif
    @endif
@endif

@includeIf('partials.sidebar', ['id' => 'right-sidebar', 'classes' => ['o-grid']])
@stop

@section('below')
@includeIf('partials.sidebar', ['id' => 'content-area-bottom', 'classes' => ['o-grid']])

<!-- Comments -->
@section('article.comments.before')@show
@includeIf('partials.comments')
@section('article.comments.after')@show

@stop
