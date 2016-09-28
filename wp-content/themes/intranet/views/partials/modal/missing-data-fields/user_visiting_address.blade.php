<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Visiting address', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-4">
                <div class="form-group">
                    <label for="user_visiting_address_place" style="font-weight:normal;"><?php _e('Workplace', 'municipio-intranet'); ?></label>
                    <input type="text" id="user_visiting_address_place" name="user_visiting_address[workplace]" value="{{ isset(get_user_meta(get_current_user_id(), 'user_visiting_address', true)['workplace']) ? get_user_meta(get_current_user_id(), 'user_visiting_address', true)['workplace'] : '' }}">
                    <?php municipio_intranet_field_example('user_visiting_address_place', 'Kontaktcenter'); ?>
                </div>
            </div>

            <div class="grid-md-4">
                <div class="form-group">
                    <label for="user_visiting_address_street" style="font-weight:normal;"><?php _e('Street address', 'municipio-intranet'); ?></label>
                    <input type="text" id="user_visiting_address_street" name="user_visiting_address[street]" value="{{ isset(get_user_meta(get_current_user_id(), 'user_visiting_address', true)['street']) ? get_user_meta(get_current_user_id(), 'user_visiting_address', true)['street'] : '' }}">
                    <?php municipio_intranet_field_example('user_visiting_address_street', 'Stortorget 7'); ?>
                </div>
            </div>

            <div class="grid-md-4">
                <div class="form-group">
                    <label for="user_visiting_address_city" style="font-weight:normal;"><?php _e('City', 'municipio-intranet'); ?></label>
                    <input type="text" id="user_visiting_address_city" name="user_visiting_address[city]" value="{{ isset(get_user_meta(get_current_user_id(), 'user_visiting_address', true)['city']) ? get_user_meta(get_current_user_id(), 'user_visiting_address', true)['city'] : '' }}">
                    <?php municipio_intranet_field_example('user_visiting_address_city', 'Helsingborg'); ?>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
