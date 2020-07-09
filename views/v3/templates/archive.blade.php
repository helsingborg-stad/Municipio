@extends('templates.single')

@section('before-layout')
    @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'top')
        <div class="o-container">
            @includeFirst(["partials.archive.archive-" . sanitize_title($postType) . "-filters",
            "partials.archive.archive-filters"])
        </div>
    @endif
@stop

@section('sidebar-left')
    @includeIf('partials.sidebar', ['id' => 'left-sidebar'])
    @includeIf('partials.sidebar', ['id' => 'left-sidebar-bottom'])
@stop

@section('content')
    <div class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive" @if (apply_filters('archive_equal_container', false, $postType, $template))  @endif>
        {!! $hook->loopStart !!}
            @include('partials.archive.archive-title')

            @if (get_field('archive_' . sanitize_title($postType) . '_filter_position', 'option') == 'content')
                @include("partials.archive.archive-filters")
            @endif

            @if (!empty($posts))
                @includeIf('partials.post.post-' . $template, ['posts' => $posts])
                
                    @pagination([
                        'list' => $paginationList, 
                        'classList' => ['u-margin__top--4'], 
                        'current' => isset($_GET['paged']) ? $_GET['paged'] : 1,
                        'linkPrefix' => '?paged='
                        ])
                @endpagination
            @else
                {{-- TODO: add a notice component --}}
                <?php _e('No posts to show', 'municipio'); ?>
            @endif
        {!! $hook->loopEnd !!}
    </div>
@stop