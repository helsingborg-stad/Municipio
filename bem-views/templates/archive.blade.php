@extends('templates.master')


@section('before-layout')

    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        @include('partials.archive-filters')
    @endif

@stop

@section('above')
    @include('components.breadcrumbs')
@stop

@section('sidebar-left')
    @include('components.dynamic-sidebar', ['id' => 'left-sidebar'])

    @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
    {!! $navigation['sidebarMenu'] !!}
    @endif

    @include('components.dynamic-sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')
    @include('components.dynamic-sidebar', ['id' => 'content-area-top'])

    @if (have_posts())
        <div class="c-archive c-archive--{{sanitize_title($postType)}} grid" @if (in_array($template, array('cards'))) data-equal-container @endif>
            <?php $postNum = 0; ?>
            @while(have_posts())
                {!! the_post() !!}
                <div class="c-archive__item grid-s-12 u-mb-4 {{ $grid_size }}">
                    @include('partials.archive.post.post-' . $template)
                </div>
                <?php $postNum++; ?>
            @endwhile
        </div>
    @else
        <div class="notice info pricon pricon-info-o pricon-space-right"><?php _e('No posts to show', 'municipio'); ?>…</div>
    @endif

    @include('components.dynamic-sidebar', ['id' => 'content-area'])
@stop

@section('sidebar-right')
    @include('components.dynamic-sidebar', ['id' => 'right-sidebar'])
@stop

@section('below')
    @include('components.dynamic-sidebar', ['id' => 'content-area-bottom'])
@stop
