<aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar">
    @if (is_active_sidebar('right-sidebar'))
    <div class="grid">
        <?php dynamic_sidebar('right-sidebar'); ?>
    </div>
    @endif
</aside>
