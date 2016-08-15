<?php do_action('customer-feedback'); ?>

<footer class="page-footer grid">
    <div class="grid-md-8">
        @include('partials.timestamps')
    </div>

    <div class="grid-md-4 text-right-lg text-right-md text-left-sm text-left-xs">
        @if (get_field('show_share', get_the_id()) !== false && get_field('page_show_share', 'option') !== false)
            @include('partials.social-share')
        @endif
    </div>
</footer>
