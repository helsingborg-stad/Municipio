@extends('templates.master')

@section('content')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="grid-md-12 grid-lg-9">
            @while(have_posts())
                <div class="grid">
                    <div class="grid-sm-12">
                            {!! the_post() !!}

                            @include('partials.blog.type.post-single')
                    </div>
                </div>

                @if (is_active_sidebar('content-area'))
                    <div class="grid sidebar-content-area">
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
            @endwhile
        </div>

        @include('partials.sidebar-right')
    </div>
</div>

@stop
