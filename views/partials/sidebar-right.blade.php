<aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar">
    @if (isset($enabledSidebarFilters) && in_array('taxonomy', $enabledSidebarFilters))
    <div class="grid">
        <div class="grid-xs-12">
            <div class="box box-filled">
                <h4 class="box-title">Taxonomy</h4>
                <div class="box-content">
                    <ul>
                    {!!
                        wp_list_categories(array(
                            'title_li' => ''
                        ))
                    !!}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (is_active_sidebar('right-sidebar'))
    <div class="grid">
        <?php dynamic_sidebar('right-sidebar'); ?>
    </div>
    @endif
</aside>
