<div class="search-result-item">
    @if (is_null(get_field('search_result_display_options', 'option')) || in_array('date', (array)get_field('search_result_display_options', 'option')))
    <span class="search-result-date">{{ $date }}</span>
    @endif

    <h3><a class="link-item {{ isset($titleClass) ? $titleClass : '' }}" href="{{ $permalink }}">{{ strip_tags($title) }}</a></h3>

    @if (in_array('image', (array)get_field('search_result_display_options', 'option')))
    <?php
    if ($thumbnail) {
        echo '<a href="' . $permalink . '"><img src="' . $thumbnail . '" class="pull-right gutter gutter-margin gutter-left material-radius"></a>';
    }
    ?>
    @endif

    @if (is_null(get_field('search_result_display_options', 'option')) || in_array('lead', (array)get_field('search_result_display_options', 'option')))
    <p>{{ $lead }}</p>
    @endif

    @if (is_null(get_field('search_result_display_options', 'option')) || in_array('url', (array)get_field('search_result_display_options', 'option')))
    <div class="search-result-info">
        <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ $permalink }}">{{ strip_tags($permalinkText) }}</a></span>
    </div>
    @endif
</div>
