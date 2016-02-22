<footer class="post-footer grid-table grid-va-top">
    <div class="grid-md-6">
        @if (get_field('blog_show_tags', 'option'))
            @include('partials.blog.post-tags')
        @endif

        @if (get_field('blog_show_categories', 'option'))
            @include('partials.blog.post-categories')
        @endif
    </div>

    @if (get_field('post_show_share', get_the_id()) !== false && get_field('page_show_share', 'option') !== false)
    <div class="grid-md-2 text-right">
        @include('partials.social-share')
    </div>
    @endif
</footer>
