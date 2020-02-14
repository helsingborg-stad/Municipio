@if (get_field('archive_' . sanitize_title($postType) . '_title', 'option'))
    @if (is_category())

        @typography([
            "variant" => "h1",
            "element" => "h1",
        ])
            get_field('archive_' . sanitize_title($postType) . '_title', 'option') . " : " . single_cat_title()
        @endtypography

    {!! category_description() !!}
    @elseif (is_date())

        @typography([
            "variant" => "h1",
            "element" => "h1",
        ])
            get_field('archive_' . sanitize_title($postType) . '_title', 'option') . " : " . the_archive_title()
        @endtypography

    @else

        @typography([
            "variant" => "h1",
            "element" => "h1",
        ])
            get_field('archive_' . sanitize_title($postType) . '_title', 'option') . " : " . the_archive_title()
        @endtypography

    @endif
@else
    @if (is_category())

        @typography([
            "variant" => "h1",
            "element" => "h1",
        ])
            single_cat_title()
        @endtypography

    {!! category_description() !!}
    @elseif (is_date())

        @typography([
            "variant" => "h1",
            "element" => "h1",
        ])
            the_archive_title()
        @endtypography

    @endif
@endif
