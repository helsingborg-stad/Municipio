<?php global $post; ?>


@includeIf('partials.post.post-header')

@if (get_field('post_single_show_featured_image') === true)
    @image([
        'src'=> municipio_get_thumbnail_source(null, array(700,700)),
        'alt' => the_title()
    ])
    @endimage

@endif

<article id="article">
    @if (isset(get_extended($post->post_content)['main']) && !empty(get_extended($post->post_content)['main']) && isset(get_extended($post->post_content)['extended']) && !empty(get_extended($post->post_content)['extended']))

        {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
        {!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}

    @else
        {!! apply_filters('the_content', $post->post_content) !!}
    @endif
</article>

@if (is_single() && is_active_sidebar('content-area'))
    @includeIf('partials.sidebar', ['id' => 'content-area'])
@endif


@includeIf('partials.post.post-footer')
