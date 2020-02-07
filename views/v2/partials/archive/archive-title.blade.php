@if (get_field('archive_' . sanitize_title($postType) . '_title', 'option'))
    @if (is_category())
        <h1 class="archive__title">{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}: {{ single_cat_title() }}</h1>
    {!! category_description() !!}
    @elseif (is_date())
        <h1 class="archive__title">{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}: {{ the_archive_title() }}</h1>
    @else
        <h1 class="archive__title">{{ get_field('archive_' . sanitize_title($postType) . '_title', 'option') }}</h1>
    @endif
@else
    @if (is_category())
        <h1 class="archive__title">{{ single_cat_title() }}</h1>
    {!! category_description() !!}
    @elseif (is_date())
        <h1 class="archive__title">{{ the_archive_title() }}</h1>
    @endif
@endif
