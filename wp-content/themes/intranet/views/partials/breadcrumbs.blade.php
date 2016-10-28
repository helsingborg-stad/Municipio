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
            <?php
                $unit = null;
                $units = get_user_meta(get_current_user_id(), 'user_administration_unit', true);

                if (is_array($units) && !empty($units)) {
                    foreach ($units as $unitId) {
                        $unit = \Intranet\User\AdministrationUnits::getAdministrationUnit($unitId);
                        $unit = \Intranet\Helper\Multisite::searchSites($unit);

                        if (!is_array($unit) || empty($unit)) {
                            continue;
                        }

                        $unit = $unit[0];
                    }
                }
            ?>

            @if ($unit)
            <a href="#" class="btn btn-sm gutter gutter-margin gutter-top gutter-sm">
                <?php _e('Go to', 'municipio-intranet'); ?> {{ substr($unit->name, -1, 1) === 's' ? $unit->name : $unit->name . 's' }} <?php _e('intranet', 'municipio-intranet'); ?>
            </a>
            @endif
        </div>
    </div>
</section>

@endif
