@if ($hasRightSidebar)
<aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar @if(is_active_sidebar('left-sidebar'))  hidden-xs hidden-sm hidden-md @endif">
    @if (is_active_sidebar('right-sidebar') || (isset($enabledSidebarFilters) && is_array($enabledSidebarFilters)))
    <div class="grid">

        @if (isset($enabledSidebarFilters) && is_array($enabledSidebarFilters))
            @include('partials.blog.taxonomy-filters')
        @endif

        <?php dynamic_sidebar('right-sidebar'); ?>
    </div>
    @endif
</aside>
@endif
