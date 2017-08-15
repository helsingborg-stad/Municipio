@if ($hasLeftSidebar)
<aside class="grid-md-4 grid-lg-3 sidebar-left-sidebar hidden-print">
    @if (is_author())
        @include('partials.author-box')
    @endif

    <!-- Use right sidebar to the left in small-ish devices -->
    @if (is_active_sidebar('left-sidebar'))
        <div class="grid sidebar-left-sidebar-top hidden-xs hidden-sm hidden-md">
            <?php dynamic_sidebar('left-sidebar'); ?>
        </div>
    @endif

    @if (get_field('nav_sub_enable', 'option'))
    {!! $navigation['sidebarMenu'] !!}
    @endif

    @if (is_active_sidebar('left-sidebar-bottom'))
        <div class="grid sidebar-left-sidebar-bottom">
            <?php dynamic_sidebar('left-sidebar-bottom'); ?>
        </div>
    @endif

    <!-- Use right sidebar to the left in small-ish devices -->
    @if (is_active_sidebar('left-sidebar')||is_active_sidebar('right-sidebar'))
        <div class="grid sidebar-left-sidebar-top hidden-lg">
            <?php dynamic_sidebar('left-sidebar'); ?>
            <?php dynamic_sidebar('right-sidebar'); ?>
        </div>
    @endif

</aside>
@endif
