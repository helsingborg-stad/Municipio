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
                $cols = 'grid-md-8 grid-lg-9';
            }
        ?>

        <div class="{{ $cols }}">

            @if (get_field('archive_' . sanitize_title($postType) . '_title', 'option') || is_category() || is_date())
            <div class="grid">
                <div class="grid-xs-12">
                    @if (get_field('archive_' . sanitize_title($postType) . '_title', 'option'))
                        @if (is_category())
                            <h1>{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}: {{ single_cat_title() }}</h1>
                        {!! category_description() !!}
                        @elseif (is_date())
                            <h1>{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}: {{ the_archive_title() }}</h1>
                        @else
                            <h1>{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}</h1>
                        @endif
                    @else
                        @if (is_category())
                            <h1>{{ single_cat_title() }}</h1>
                        {!! category_description() !!}
                        @elseif (is_date())
                            <h1>{{ the_archive_title() }}</h1>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            @if (is_active_sidebar('content-area-top'))
                <div class="grid grid--columns sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            <div class="grid">
                @if (have_posts())
                    <?php $postNum = 0; ?>
                    <ul class="grid-md-12 event-archive">
                        @while(have_posts())
                            {!! the_post() !!}
                            <?php global $post; ?>
                                <li>
                                    <div class="grid">
                                        <div class="grid-md-9">
                                        <h2><a href="{{ esc_url(add_query_arg('date', preg_replace('/\D/', '', $post->start_date), the_permalink())) }}">{{ the_title() }}</a></h2>
                                            <div class="event-archive_meta">
                                                <p><small><i class="pricon pricon-calendar"></i>
                                                <strong><?php _ex('Date', 'Event archive', 'municipio'); ?>:</strong>
                                                   {{ \Municipio\Helper\Event::formatEventDate($post->start_date, $post->end_date) }}
                                                </small></p>

                                                <?php $location = get_field('location'); ?>
                                                @if (!empty($location['title']))
                                                    <p><small><i class="pricon pricon-location-pin"></i>
                                                    <strong><?php _ex('Location', 'Event archive','municipio'); ?>:</strong> {{ $location['title'] }}
                                                    </small></p>
                                                @endif
                                            </div>

                                        @if ($post->content_mode == 'custom' && ! empty($post->content))
                                            <p>{{ wp_trim_words($post->content, 50, ' [...]') }}</p>
                                        @else
                                            {{ the_excerpt() }}
                                        @endif

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
                <div class="grid grid--columns sidebar-content-area sidebar-content-area-bottom">
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
