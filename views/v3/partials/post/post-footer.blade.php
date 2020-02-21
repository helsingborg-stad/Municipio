{{-- TODO: NEED COMPONENT FIX --}}
<footer class="post-footer">
    @if (get_field('post_show_share', get_the_id()) !== false
        && get_field('page_show_share', 'option') !== false && is_single())

        @icon(['icon' => 'share', 'size' => 'sm'])
        @endicon
        <strong><?php _e('Share the page', 'municipio'); ?>:</strong> {{ the_title() }}

    @endif

    @if (!empty(municipio_post_taxonomies_to_display(get_the_id())))

        @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
            @includeIf('partials.blog.post-terms')
        @endforeach

    @endif


    @if (in_array('author', (array) get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option'))
        && get_field('post_show_author', get_the_id()) !== false)

        <strong><?php echo apply_filters('Municipio/author_display/title', __('Published by', 'municipio')); ?>
            :</strong>
        @if (get_field('page_link_to_author_archive', 'option'))
            <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}"
               class="post-author post-author-margin-left">
                @else
                    <span class="post-author post-author-margin-left">
                @endif
                        @if (in_array('author_image', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && get_field('post_show_author_image', get_the_id()) !== false && !empty(get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))))
                            <span class="post-author-image"
                                  style="background-image:url('{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID')) }}');">
                                        @image([
                                            'src'=> get_field('user_profile_picture', 'user_' . get_the_author_meta('ID')),
                                            'alt' => (!empty(get_the_author_meta('first_name')) &&
                                                        !empty(get_the_author_meta('last_name'))) ?
                                                        get_the_author_meta('first_name') . ' ' .
                                                        get_the_author_meta('last_name')  : get_the_author() ,
                                            'caption' => "Hey, I am a caption for an image",
                                        ])
                                        @endimage
                                    </span>
                        @endif

                        @if (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name')))
                            <span
                                class="post-author-name">{!! apply_filters('Municipio/author_display/name',
                                    get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name'),
                                    get_the_author_meta('ID')) !!}</span>
                        @else
                            <span
                                class="post-author-name">{!! apply_filters('Municipio/author_display/name',
                                            get_the_author(), get_the_author_meta('ID')) !!}</span>
                @endif

                @if (get_field('page_link_to_author_archive', 'option'))
            </a>
            @else
            </span>
        @endif

    @endif

</footer>
