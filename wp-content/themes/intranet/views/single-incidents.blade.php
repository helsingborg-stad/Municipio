@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid {{ (wp_get_post_parent_id(get_the_id()) != 0) ? 'no-margin-top' : '' }}">
        @include('partials.sidebar-left')

        <div class="{{ $contentGridSize }} print-grow">
            @if (is_single() && is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            <div class="grid">
                <div class="grid-sm-12">
                    @while(have_posts())
                        {!! the_post() !!}

                        <?php global $post; ?>
                        <div class="grid">
                            <div class="grid-xs-12">
                                <div class="post post-single">
                                    <header class="post-header">
                                        <h1 style="padding-bottom: 0.5em;" class="pricon pricon-notice-{{ $post->incident_level }} pricon-space-right notice notice-inline-block notice-lg {{ $post->incident_level }}">{{ the_title() }}</h1>

                                        @include('partials.blog.post-info')
                                    </header>

                                    <article>
                                        @if (isset(get_extended($post->post_content)['main']) && strlen(get_extended($post->post_content)['main']) > 0 && isset(get_extended($post->post_content)['extended']) && strlen(get_extended($post->post_content)['extended']) > 0)

                                            {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
                                            {!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}

                                        @else
                                            {!! the_content() !!}
                                        @endif
                                    </article>
                                </div>
                            </div>
                        </div>

                        @include('partials.blog.post-footer')
                    @endwhile
                </div>
            </div>

            @if (is_single() && is_active_sidebar('content-area'))
                <div class="grid sidebar-content-area sidebar-content-area-bottom">
                    <?php dynamic_sidebar('content-area'); ?>
                </div>
            @endif

            @if (is_single() && comments_open())
                <div class="grid">
                    <div class="grid-sm-12">
                        @include('partials.blog.comments-form')
                    </div>
                </div>
                <div class="grid">
                    <div class="grid-sm-12">
                        @include('partials.blog.comments')
                    </div>
                </div>
            @endif
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop
