@if (!empty($enabledFilters))
<section class="creamy creamy-border-bottom gutter-lg gutter-vertical sidebar-content-area">
    <form method="get" action="" class="container" id="archive-filter">
        <div class="grid">
            @if (in_array('text_search', $enabledFilters))
            <div class="grid-md-{{ 10/count($enabledFilters) }}">
                <label for="filter-keyword" class="text-sm"><strong><?php _e('Title', 'municipio'); ?>:</strong></label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="text" name="s" id="filter-keyword" class="form-control" value="{{ isset($_GET['s']) && !empty($_GET['s']) ? $_GET['s'] : '' }}">
                </div>
            </div>
            @endif

            @if (in_array('date_range', $enabledFilters))
            <div class="grid-md-{{ 10/count($enabledFilters) }}">
                <label for="filter-date-from" class="text-sm"><strong><?php _e('Date published', 'municipio'); ?>:</strong></label>
                <div class="input-group">
                    <span class="input-group-addon"><?php _e('From', 'municipio'); ?>:</span>
                    <input type="text" name="from" placeholder="<?php _e('From date', 'municipio'); ?>…" id="filter-date-from" class="form-control datepicker-range datepicker-range-from" value="{{ isset($_GET['from']) && !empty($_GET['from']) ? $_GET['from'] : '' }}" readonly>
                    <span class="input-group-addon"><?php _e('To', 'municipio'); ?>:</span>
                    <input type="text" name="to" placeholder="<?php _e('To date', 'municipio'); ?>" class="form-control datepicker-range datepicker-range-to" value="{{ isset($_GET['to']) && !empty($_GET['to']) ? $_GET['to'] : '' }}" readonly>
                </div>
            </div>
            @endif

            <div class="grid-md-2">
                 <label for="filter-date-from" class="text-sm">&nbsp;</label>
                <input type="submit" value="<?php _e('Filter', 'municipio'); ?>" class="btn btn-primary btn-block">
            </div>
        </div>
    </form>
</section>
@endif
