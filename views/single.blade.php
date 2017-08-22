@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="grid-md-12 grid-lg-9">
            @if (is_single() && is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            <div class="grid">
                <div class="grid-sm-12">
                        {!! the_post() !!}

                        @include('partials.blog.type.post-single')
                </div>
            </div>

            @if (is_single() && comments_open() && is_user_logged_in() && get_option('comment_order') == 'desc')
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
            @elseif (is_single() && comments_open() && is_user_logged_in() && get_option('comment_order') == 'asc')
                <div class="grid">
                    <div class="grid-sm-12">
                        @include('partials.blog.comments')
                    </div>
                </div>
                <div class="grid">
                    <div class="grid-sm-12">
                        @include('partials.blog.comments-form')
                    </div>
                </div>
            @endif
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop

