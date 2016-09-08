<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Email', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-12">
                <p><?php _e('You need to add you\'r email address to your account.', 'municipio-intranet'); ?></p>
            </div>
        </div>

        <div class="grid">
            <div class="grid-xs-12">
                <div class="form-group">
                    <label for="user_email"><?php _e('Email', 'municipio-intranet'); ?><span class="text-danger">*</span></label>
                    <input type="email" id="user_email" name="user_email" value="{{ get_the_author_meta('email') }}" required>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
