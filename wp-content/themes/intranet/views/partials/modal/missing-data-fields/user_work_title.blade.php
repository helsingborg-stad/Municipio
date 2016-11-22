<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Work title', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-12">
                <p><?php _e('Please fill in your work title.', 'municipio-intranet'); ?></p>
            </div>
        </div>

        <div class="grid">
            <div class="grid-xs-12">
                <div class="form-group">
                    <label for="user_work_title"><?php _e('Work title', 'municipio-intranet'); ?><span class="text-danger">*</span> <?php echo !empty(get_the_author_meta('ad_title')) ? '<small>(' . __('Not editable', 'municipio-intranet') . ')</small>' : ''; ?></label>
                    @if (!empty(get_the_author_meta('ad_title')))
                    <input type="text" id="user_work_title" name="ad_title" value="{{ get_the_author_meta('ad_title') }}" disabled required>
                    @else
                    <input type="text" id="user_work_title" name="user_work_title" value="{{ get_the_author_meta('user_work_title') }}" required>
                    @endif
                    <?php municipio_intranet_field_example('user_work_title', __('Project coordinator', 'municipio-intranet')); ?>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
