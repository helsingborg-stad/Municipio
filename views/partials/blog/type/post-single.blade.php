<?php global $post; ?>
<div class="grid">
    <div class="grid-xs-12">
        <div class="post post-single">
            @include('partials.blog.post-header')

            <article>
                @if (isset(get_extended($post->post_content)['main']) && strlen(get_extended($post->post_content)['main']) > 0 && isset(get_extended($post->post_content)['extended']) && strlen(get_extended($post->post_content)['extended']) > 0)

                    {!! apply_filters('the_lead', get_extended($post->post_content)['main']) !!}
                    {!! apply_filters('the_content', get_extended($post->post_content)['extended']) !!}

                @else
                    {!! the_content() !!}
                @endif
            </article>
        </div>
    </div>
</div>

@include('partials.blog.post-footer')
