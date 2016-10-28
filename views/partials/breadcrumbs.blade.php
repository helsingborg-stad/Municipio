@if (apply_filters('Municipio/Breadcrumbs', wp_get_post_parent_id(get_the_id()) != 0, get_queried_object()))
<div class="grid breadcrumbs-wrapper">
    <div class="grid-lg-12">
        {{ \Municipio\Theme\Navigation::outputBreadcrumbs() }}
    </div>
</div>
@endif
