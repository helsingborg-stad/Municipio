<ul class="article-timestamps">
    @if (get_field('page_show_author', 'option') !== false && get_field('post_show_author', get_the_id()) !== false && get_the_author())
        <li>
            <strong><?php echo apply_filters('Municipio/author_display/title', __('Published by', 'municipio')); ?>:</strong>
            @if (get_field('page_link_to_author_archive', 'option'))
            <a href="{{ get_author_posts_url(get_the_author_meta('ID')) }}" class="post-author post-author-margin-left">
            @else
            <span class="post-author post-author-margin-left">
            @endif

                @if (in_array('author_image', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && get_field('post_show_author_image', get_the_id()) !== false && !empty(get_field('user_profile_picture', 'user_' . get_the_author_meta('ID'))))
                    <span class="post-author-image" style="background-image:url('{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID')) }}');"><img src="{{ get_field('user_profile_picture', 'user_' . get_the_author_meta('ID')) }}" alt="{{ (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name'))) ? get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name')  : get_the_author() }}"></span>
                @endif

                @if (!empty(get_the_author_meta('first_name')) && !empty(get_the_author_meta('last_name')))
                    <span class="post-author-name">{!! apply_filters('Municipio/author_display/name', get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name'), get_the_author_meta('ID')) !!}</span>
                @else
                    <span class="post-author-name">{!! apply_filters('Municipio/author_display/name', get_the_author(), get_the_author_meta('ID')) !!}</span>
                @endif

            @if (get_field('page_link_to_author_archive', 'option'))
            </a>
            @else
            </span>
            @endif
        </li>
    @endif

    @if (get_the_modified_time() != get_the_time())

            @if(is_array(get_field('show_date_published','option')) && in_array(get_post_type(get_the_id()),get_field('show_date_published','option')))
            <li>
                <strong><?php _e("Published", 'municipio'); ?>:</strong>
                <time datetime="<?php echo the_time('Y-m-d H:i'); ?>">
                    <?php the_time('j F Y'); ?> kl. <?php the_time('H:i'); ?>
                </time>
            </li>
            @endif

            @if(is_array(get_field('show_date_updated','option')) && in_array(get_post_type(get_the_id()), get_field('show_date_updated','option')))
            <li>
                <strong><?php _e("Last updated", 'municipio'); ?>:</strong>
                <time datetime="<?php echo the_modified_time('Y-m-d H:i'); ?>">
                    <?php the_modified_time('j F Y'); ?> kl. <?php the_modified_time('H:i'); ?>
                </time>
            </li>
            @endif

    @else

        @if (is_array(get_field('show_date_published','option')) && in_array(get_post_type(get_the_id()), get_field('show_date_published','option')))
            <li>
                <strong><?php _e("Published", 'municipio'); ?>:</strong>
                <time datetime="<?php echo the_time('Y-m-d H:i'); ?>">
                    <?php the_time('j F Y'); ?> {!! __("at", 'municipio'); !!} <?php the_time('H:i'); ?>
                </time>
            </li>
        @endif

    @endif
</ul>
