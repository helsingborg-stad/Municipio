@if (apply_filters('Municipio/show_breadcrumbs', wp_get_post_parent_id(get_the_id()), get_queried_object()) != 0)
<div class="grid breadcrumbs-wrapper">
    <div class="grid-lg-12">
        {{ \Municipio\Theme\Navigation::outputBreadcrumbs() }}
    </div>
</div>
@endif
