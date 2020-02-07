<footer class="post-footer">
    @if (get_field('post_show_share', get_the_id()) !== false && get_field('page_show_share', 'option') !== false && is_single())
        <div class="grid u-mb-4">
            <div class="grid-xs-12">
                <div class="box box-border gutter gutter-horizontal no-margin">
                    <div class="gutter gutter-vertical gutter-sm">
                    <div class="grid grid-table grid-va-middle no-margin no-padding">
                        <div class="grid-md-8">
                            <i class="pricon pricon-share pricon-lg" style="margin-right:5px;"></i> <strong><?php _e('Share the page', 'municipio'); ?>:</strong> {{ the_title() }}
                        </div>
                        <div class="grid-md-4 text-right-md text-right-lg">
                            @include('partials.social-share')
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (!empty(municipio_post_taxonomies_to_display(get_the_id())))
    <div class="grid grid-table">
        <div class="grid-md-12">
            @foreach (municipio_post_taxonomies_to_display(get_the_id()) as $taxonomy => $terms)
                @include('partials.blog.post-terms')
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-table grid-table-autofit {{ is_single() ? 'no-padding' : '' }}">
        @if (in_array('author', (array) get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) && get_field('post_show_author', get_the_id()) !== false)
            <div class="grid-md-6">
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
            </div>
        @endif

        @if (get_field('post_show_share', get_the_id()) !== false && get_field('page_show_share', 'option') !== false && !is_single())
        <div class="{{ in_array('author', (array) get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')) ? 'grid-md-6' : 'grid-md-12' }} text-right">
            @include('partials.social-share')
        </div>
        @endif
    </div>
</footer>
