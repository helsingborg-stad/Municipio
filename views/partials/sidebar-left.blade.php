@if (get_field('nav_sub_enable', 'option') === true)
<aside class="grid-md-4 grid-lg-3 sidebar-left-sidebar">
    {!! $navigation['sidebarMenu'] !!}

    @if (is_active_sidebar('left-sidebar'))
        <div class="grid">
            <?php dynamic_sidebar('left-sidebar'); ?>
        </div>
    @endif
</aside>
@endif
