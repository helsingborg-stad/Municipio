<aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar">
    @if (is_active_sidebar('right-sidebar') || (isset($enabledSidebarFilters) && is_array($enabledSidebarFilters)))
    <div class="grid">
        @if (isset($enabledSidebarFilters) && is_array($enabledSidebarFilters))
        @foreach ($enabledSidebarFilters as $taxonomy)
        <?php $taxs = get_terms($taxonomy); ?>
        @if (count($taxs) > 0)
        <div class="grid-xs-12">
            <div class="box box-filled">
                <h4 class="box-title">{{ get_taxonomy($taxonomy)->labels->name }}</h4>
                <div class="box-content">
                    <ul>
                    @foreach ($taxs as $tax)
                        <li><a href="#">{{ $tax->name }}</a></li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @endforeach
        @endif

        <?php dynamic_sidebar('right-sidebar'); ?>
    </div>
    @endif
</aside>
