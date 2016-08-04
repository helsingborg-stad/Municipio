<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Phone number', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-12">
                <p><?php _e('If you want other users to be able to find your phone number on your profile you will need to add your phone number below.', 'municipio-intranet'); ?></p>
            </div>
        </div>

        <div class="grid">
            <div class="grid-xs-12">
                <div class="form-group">
                    <label for="user_phone"><?php _e('Phone number', 'municipio-intranet'); ?></label>
                    <input type="text" id="user_phone" name="user_phone" value="{{ get_the_author_meta('user_phone') }}" pattern="^\+?([\d|\s|\(|\)|\-])+" oninvalid="this.setCustomValidity('<?php _e('The phone number supplied is invalid', 'municipio-intranet'); ?>')">
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
