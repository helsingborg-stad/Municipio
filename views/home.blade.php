@extends('templates.master')

@section('content')

@include('partials.archive-filters')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <?php
        $cols = 'grid-md-12';
        if (is_active_sidebar('right-sidebar') && get_field('archive_post_show_sidebar_navigation', 'option')) {
            $cols = 'grid-md-8 grid-lg-6';
        } elseif (is_active_sidebar('right-sidebar') || get_field('archive_post_show_sidebar_navigation', 'option')) {
            $cols = 'grid-md-12 grid-lg-9';
        }
    ?>

    <div class="grid">
        <div class="{{ $cols }}">
            <div class="grid" data-equalize-container>
                @if (have_posts())
                    @while(have_posts())
                        {!! the_post() !!}

                        @if ($template == 'full')
                            @include('partials.blog.type.post')
                        @else
                            @include('partials.blog.type.post-' . $template)
                        @endif
                    @endwhile
                @else
                    <div class="grid-sm-12"><i class="fa fa-frown-o"></i> <?php _e('No posts found…', 'municipio'); ?></div>
                @endif
            </div>

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
