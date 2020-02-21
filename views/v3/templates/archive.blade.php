@extends('templates.master')


@section('before-layout')

    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        @includeFirst(["partials.archive.archive-" . sanitize_title($postType) . "-filters",
        "partials.archive.archive-filters"])
    @endif

@stop

@section('above')
    @breadcrumb([
        'list' => \Municipio\Theme\Navigation::breadcrumbData()
    ])
    @endbreadcrumb
@stop

@section('sidebar-left')

    @includeIf('partials.sidebar.default', ['id' => 'left-sidebar'])
    @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
        {!! $navigation['sidebarMenu'] !!}
    @endif

    @includeIf('partials.sidebar.default', ['id' => 'left-sidebar-bottom'])

@stop

@section('content')
    @includeIf('partials.sidebar.default', ['id' => 'content-area-top'])

    @include('partials.archive.archive-title')

    @if (have_posts())
        <div
            class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive grid grid--columns"
            @if (apply_filters('archive_equal_container', false, $postType, $template)) data-equal-container @endif>

            @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                @includeFirst(["partials.archive.archive-" . sanitize_title($postType) .
                "-filters", "partials.archive-filters"])
            @endif


            <?php $postNum = 0; ?>
            @while(have_posts())
                {!! the_post() !!}
                <div class="grid-xs-12 {{ $grid_size }}">
                    @includeIf('partials.post.post-' . $template)
                </div>
                <?php $postNum++; ?>
            @endwhile
        </div>
    @else
        {{-- TODO: add a notice component --}}
        <?php _e('No posts to show', 'municipio'); ?>
    @endif


    @includeIf('partials.sidebar.default', ['id' => 'content-area'])


    {!!
        paginate_links(array(
            'type' => 'list'
        ))
    !!}

@stop


@section('sidebar-right')
    @includeIf('partials.sidebar.default', ['id' => 'right-sidebar'])
@stop


@section('below')
    @includeIf('partials.sidebar.default', ['id' => 'content-area-bottom'])
@stop
