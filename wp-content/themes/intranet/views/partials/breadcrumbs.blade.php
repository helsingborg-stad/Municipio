@if (!is_main_site())

<div class="grid no-margin-top">
    <div class="grid-xs-12">
        <div class="breadcrumbs-wrapper">
            <div class="grid">
                @if (apply_filters('Municipio/Breadcrumbs', wp_get_post_parent_id(get_the_id()) != 0, get_queried_object()))
                <div class="grid-md-8">
                    {{ \Municipio\Theme\Navigation::outputBreadcrumbs() }}
                </div>
                @endif

                @include('partials.header.subscribe')
            </div>
        </div>
    </div>
</div>

@else

<section class="gutter gutter-vertical gutter-xl">
    <div class="grid">
        <div class="grid-xs-12 text-center">
            <span class="h1 greeting">{!! \Intranet\User\General::greet() !!}</span>
        </div>
    </div>
</section>

@endif
