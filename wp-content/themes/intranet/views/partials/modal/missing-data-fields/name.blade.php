<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('First name', 'municipio-intranet'); ?> &amp; <?php _e('Last name', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-6">
                <div class="form-group">
                    <label for="user_first_name">
                        <?php _e('First name', 'municipio-intranet'); ?>
                        @if (get_user_meta(get_current_user_id(), 'first_name', true))
                        <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small>
                        @endif
                    </label>
                    <input type="text" id="user_first_name" name="first_name" value="{{ get_user_meta(get_current_user_id(), 'first_name', true) }}" {{ get_user_meta(get_current_user_id(), 'first_name', true) ? 'disabled' : '' }}>
                </div>
            </div>
            <div class="grid-md-6">
                <div class="form-group">
                    <label for="user_last_name">
                        <?php _e('Last name', 'municipio-intranet'); ?>
                        @if (get_user_meta(get_current_user_id(), 'last_name', true))
                        <small>(<?php _e('Not editable', 'municipio-intranet'); ?>)</small>
                        @endif
                    </label>
                    <input type="text" id="user_last_name" name="last_name" value="{{ get_user_meta(get_current_user_id(), 'last_name', true) }}" {{ get_user_meta(get_current_user_id(), 'last_name', true) ? 'disabled' : '' }}>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
