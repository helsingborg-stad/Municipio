<?php global $post; ?>
<div class="post post-full">
    @include('partials.blog.post-header')

    @if (is_null(get_field('post_single_show_featured_image')) || get_field('post_single_show_featured_image') === true)
        <img src="{{ municipio_get_thumbnail_source(null, array(700,700)) }}" alt="{{ the_title() }}">
    @endif

    <article>
        @if (isset(get_extended($post->post_content)['main']) && strlen(get_extended($post->post_content)['main']) > 0 && isset(get_extended($post->post_content)['extended']) && strlen(get_extended($post->post_content)['extended']) > 0)

            {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
            {!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}

        @else
            {!! the_content() !!}
        @endif
    </article>

    @include('partials.blog.post-footer')
</div>
