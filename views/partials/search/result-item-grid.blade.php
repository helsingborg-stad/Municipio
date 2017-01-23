<div class="{{ $gridSize }}">
    <a href="{{ $permalink }}" class="box box-post-brick">
        @if (in_array('image', (array)get_field('search_result_display_options', 'option')) && $thumbnail)
        <div class="box-image" {!! $thumbnail ? 'style="background-image:url(' . $thumbnail . ');"' : '' !!}>
            <img src="{{ $thumbnail }}" alt="{{ $title }}">
        </div>
        @endif

        <div class="box-content">
            @if (is_null(get_field('search_result_display_options', 'option')) || in_array('date', (array)get_field('search_result_display_options', 'option')))
            <span class="box-post-brick-date">
                <time>
                    {{ mysql2date(get_option('date_format'), $date) }}
                    {{ mysql2date(get_option('time_format'), $date) }}
                </time>
            </span>
            @endif

            <h3 class="post-title">{{ $title }}</h3>
        </div>

        @if (is_null(get_field('search_result_display_options', 'option')) || in_array('lead', (array)get_field('search_result_display_options', 'option')))
        <div class="box-post-brick-lead">
            {{ $lead }}
        </div>
        @endif
    </a>
</div>
