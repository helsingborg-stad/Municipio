@extends('templates.master')

@section('content')

@include('partials.archive-filters')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        @if (get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option'))
            @include('partials.sidebar-left')
        @endif

        <?php
            $cols = 'grid-md-12';
            if (is_active_sidebar('right-sidebar') && get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option')) {
                $cols = 'grid-md-8 grid-lg-6';
            } elseif (is_active_sidebar('right-sidebar') || get_field('archive_' . sanitize_title($postType) . '_show_sidebar_navigation', 'option')) {
                $cols = 'grid-md-12 grid-lg-9';
            }
        ?>

        <div class="{{ $cols }}">

            @if (is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif


            <div class="grid">
                @if (have_posts())
                    <?php $postNum = 0; ?>
                    <ul class="grid-md-12 archive_event">
                        @while(have_posts())
                            {!! the_post() !!}
                            <?php global $post; ?>
                                <li>
                                    <div class="grid">
                                        <div class="grid-md-9">
                                            <h2><a href="{{ the_permalink() }}">{{ the_title() }}</a></h2>
                                            <div class="archive_event_meta">
                                                <p><small>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 512 512"><path d="M144 128c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v32c0 17.7 14.3 32 32 32zm224 0c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32s-32 14.3-32 32v32c0 17.7 14.3 32 32 32z"/><path d="M472 64h-56v40.7c0 22.5-23.2 39.3-47.2 39.3S320 127.2 320 104.7V64H192v40.7c0 22.5-24 39.3-48 39.3s-48-16.8-48-39.3V64H40c-4.4 0-8 3.6-8 8v400c0 4.4 3.6 8 8 8h432c4.4 0 8-3.6 8-8V72c0-4.4-3.6-8-8-8zm-40 368H80V176h352v256z"/></svg>
                                                    <strong><?php _e('Date', 'hoor'); ?>:</strong>

                                                   {{ \Municipio\Controller\Archive::formatEventDate($post->start_date, $post->end_date) }}
                                                </small></p>

                                                <?php $location = get_field('location'); ?>
                                                @if (!empty($location['title']))
                                                    <p><small>
                                                        <svg width="14" height="16" viewBox="0 0 14 20" xmlns="http://www.w3.org/2000/svg"><path d="M7 0C3.13 0 0 3.13 0 7c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"fill-rule="evenodd"/></svg>
                                                        <strong><?php _e('Location', 'hoor'); ?>:</strong> <?php echo $location['title'] ?>
                                                    </small></p>
                                                @endif
                                            </div>
                                        {{ the_excerpt() }}
                                        </div>
                                        @if (municipio_get_thumbnail_source(null,array(400,250)))
                                            <div class="grid-md-3">
                                                <img src="{{ municipio_get_thumbnail_source(null,array(400,250)) }}">
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            <?php $postNum++; ?>
                        @endwhile
                    </ul>
                @else
                    <div class="grid-xs-12">
                        <div class="notice info pricon pricon-info-o pricon-space-right"><?php _e('No posts to show', 'municipio'); ?>…</div>
                    </div>
                @endif
            </div>



            @if (is_active_sidebar('content-area'))
                <div class="grid sidebar-content-area sidebar-content-area-bottom">
                    <?php dynamic_sidebar('content-area'); ?>
                </div>
            @endif

            <div class="grid">
                <div class="grid-sm-12 text-center">
                    {!!
                        paginate_links(array(
                            'type' => 'list'
                        ))
                    !!}
                </div>
            </div>
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop
