<footer class="post-footer grid-table grid-va-top">
    <div class="grid-md-6">
        @include('partials.blog.post-tags')
    </div>

    @if (get_field('show_share', get_the_id()) != 'false' && get_field('show_share_master', 'option') != 'false')
    <div class="grid-md-2 text-right">
        @include('partials.social-share')
    </div>
    @endif
</footer>
