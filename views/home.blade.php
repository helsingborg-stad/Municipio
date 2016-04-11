@extends('templates.master')

@section('content')

@include('partials.archive-filters')

<div class="container main-container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="{{ is_active_sidebar('right-sidebar') ? 'grid-md-12 grid-lg-9' : 'grid-md-12' }}">
            <div class="grid" data-equalize-container>
                @if (have_posts())
                    @while(have_posts())
                        {!! the_post() !!}

                        @if (get_field('blog_feed_post_style', 'option') == 'full' || !get_field('blog_feed_post_style', 'option'))
                            @include('partials.blog.type.post')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'collapsed')
                            @include('partials.blog.type.post-collapsed')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'compressed')
                            @include('partials.blog.type.post-compressed')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'grid')
                            @include('partials.blog.type.post-grid')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'cards')
                            @include('partials.blog.type.post-cards')
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

        @if (is_active_sidebar('right-sidebar'))
        @include('partials.sidebar-right')
        @endif
    </div>
</div>

@stop
