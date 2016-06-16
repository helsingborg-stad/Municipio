@extends('templates.master')

@section('content')

<div class="container main-container">

    @include('partials.breadcrumbs')

    <div class="grid {{ (wp_get_post_parent_id(get_the_id()) != 0) ? 'no-margin-top' : '' }}">
        @include('partials.sidebar-left')

        <div class="{{ $contentGridSize }} print-grow" id="readspeaker-read">

            @if (is_active_sidebar('content-area-top'))
                <div class="grid sidebar-content-area sidebar-content-area-top">
                    <?php dynamic_sidebar('content-area-top'); ?>
                </div>
            @endif

            @if (is_main_site())
                @if (is_user_logged_in())
                <h1 class="gutter gutter-bottom">{{ \Intranet\User\General::greet() }}</h1>
                @endif
            @else
                @if (have_posts())
                    @while(have_posts())
                        {!! the_post() !!}

                        @include('partials.article')
                    @endwhile
                @endif
            @endif

            @if (is_active_sidebar('content-area'))
                <div class="grid sidebar-content-area sidebar-content-area-bottom">
                    <?php dynamic_sidebar('content-area'); ?>
                </div>
            @endif
        </div>


        @include('partials.sidebar-right')
    </div>
</div>

@stop

