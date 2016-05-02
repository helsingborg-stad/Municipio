<footer class="post-footer grid-table grid-va-top">
    <div class="grid-md-8">
        @if (in_array('tags', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')))
            @include('partials.blog.post-tags')
        @endif

        @if (in_array('category', (array)get_field('archive_' . sanitize_title(get_post_type()) . '_post_display_info', 'option')))
            @include('partials.blog.post-categories')
        @endif
    </div>

    @if (get_field('post_show_share', get_the_id()) !== false && get_field('page_show_share', 'option') !== false)
    <div class="grid-md-4 text-right">
        @include('partials.social-share')
    </div>
    @endif
</footer>
