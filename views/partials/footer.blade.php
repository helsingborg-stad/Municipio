@if (is_active_sidebar('bottom-sidebar'))
    <?php dynamic_sidebar('bottom-sidebar'); ?>
@endif

@include('partials.footer.footer-' . $footerLayout)
