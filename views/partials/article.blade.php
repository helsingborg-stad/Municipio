<?php global $post; ?>
<article class="clearfix">
    <h1>{{ the_title() }}</h1>

    @include('partials.accessibility-menu')

    @if (is_null(get_field('post_single_show_featured_image')) || get_field('post_single_show_featured_image') === true)
        <img src="{{ municipio_get_thumbnail_source(null, array(700,700)) }}" alt="{{ the_title() }}">
    @endif

    @if (isset(get_extended($post->post_content)['main']) && strlen(get_extended($post->post_content)['main']) > 0 && isset(get_extended($post->post_content)['extended']) && strlen(get_extended($post->post_content)['extended']) > 0)

        {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
        {!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}

    @else
        @if (substr($post->post_content, -11) == '<!--more-->')
        {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
        @else
        {!! the_content() !!}
        @endif

    @endif

</article>
