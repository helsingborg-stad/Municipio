@extends('templates.master')

@section('content')

<div class="container">
    @include('partials.breadcrumbs')

    <div class="grid">
        <div class="grid-md-8 grid-lg-8">
            <div class="grid">
                <div class="grid-sm-12">
                    @while(have_posts())
                        {!! the_post() !!}

                        @if (get_field('blog_feed_post_style', 'option') == 'full' || !get_field('blog_feed_post_style', 'option'))
                            @include('partials.blog.type.post')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'collapsed')
                            @include('partials.blog.type.post-collapsed')
                        @elseif(get_field('blog_feed_post_style', 'option') == 'compressed')
                            @include('partials.blog.type.post-compressed')
                        @endif
                    @endwhile
                </div>
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
