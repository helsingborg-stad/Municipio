<div class="search-result-item">

    @if($label)
    <span class="network-title label label-sm label-theme">{{ $label }}</span>
    @endif
    <h3>
        <a class="link-item {{ isset($titleClass) ? $titleClass : '' }}" href="{{ $permalink }}">{{ strip_tags($title) }}</a>
    </h3>

    @if (is_null(get_field('search_result_display_options', 'option')) || in_array('lead', (array)get_field('search_result_display_options', 'option')))
    <p>{!! $lead !!}</p>
    @endif

    @if (is_null(get_field('search_result_display_options', 'option')) || in_array('url', (array)get_field('search_result_display_options', 'option')))
    <div class="search-result-info">
        <span class="search-result-url"><i class="fa fa-globe"></i> <a href="{{ $permalink }}">{{ strip_tags($permalinkText) }}</a></span>
    </div>
    @endif
</div>
