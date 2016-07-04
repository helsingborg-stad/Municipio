<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('About me', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid no-padding">
            <div class="grid-xs-12">
                <div class="form-group" style="margin:-20px;">
                    <label for="user_about" class="sr-only"><?php _e('About me', 'municipio-intranet'); ?></label>
                    <textarea name="user_about" id="user_about" cols="30" rows="10" style="border:none;display: block;box-shadow: none;padding: 20px;" placeholder="<?php _e('Write a little something about yourself…', 'municipio-intranet'); ?>">{{ get_the_author_meta('user_about') }}</textarea>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
