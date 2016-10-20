<?php $administrationUnits = \Intranet\User\AdministrationUnits::getAdministrationUnits(); ?>

<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Department', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-12">
                <p><?php _e('We need to know which department you\'re working for.', 'municipio-intranet'); ?></p>
            </div>
        </div>

        <div class="grid">
            <div class="grid-xs-12">
                <div class="form-group">
                    <label for="user_administration_unit"><?php _e('Administration unit', 'municipio-intranet'); ?><span class="text-danger">*</span></label>
                    <div class="grid">
                        @foreach ($administrationUnits as $unit)
                        <div class="grid-md-6">
                            <label class="checkbox">
                                <input type="checkbox" name="user_administration_unit[]" value="{{ $unit->id }}" {{ checked(true, in_array($unit->id, (array)get_the_author_meta('user_administration_unit'))) }} data-require>
                                {{ $unit->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="grid-xs-12">
                <div class="form-group">
                    <label for="user_department"><?php _e('Department', 'municipio-intranet'); ?><span class="text-danger">*</span></label>
                    <input type="text" id="user_department" name="user_department" value="{{ get_the_author_meta('user_department') }}" required>
                    <?php municipio_intranet_field_example('user_department', __('Web-department', 'municipio-intranet')); ?>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
