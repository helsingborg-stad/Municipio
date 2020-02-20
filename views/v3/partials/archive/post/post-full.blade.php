
{{-- TODO: reformat as card component  (WHAT TODO WITH THE INCLUDES!!?!?!?!?!?!?)--}}
<?php global $post; ?>
<div class="c-card">
    @if (get_field('post_single_show_featured_image') === true)
        <img class="c-card__image" src="{{ municipio_get_thumbnail_source(null, array(700,700)) }}" alt="{{ the_title() }}">
    @endif

    <div class="c-card__body u-pb-0">
        @includeIf('partials.archive.post-header')
    </div>
    <div class="c-card__body">
        <div class="c-card__text">
            @if (isset(get_extended($post->post_content)['main']) && strlen(get_extended($post->post_content)['main']) > 0 && isset(get_extended($post->post_content)['extended']) && strlen(get_extended($post->post_content)['extended']) > 0)

                {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
                {!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}

            @else
                {!! the_content() !!}
            @endif
        </div>
    </div>

    <div class="c-card__footer">
        @includeIf('partials.archive.post-footer')

    </div>
</div>








