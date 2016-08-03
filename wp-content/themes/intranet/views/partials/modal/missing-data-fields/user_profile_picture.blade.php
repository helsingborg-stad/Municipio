<section class="accordion-section">
    <input type="radio" name="active-section" id="accordion-missing-{{ $i }}">
    <span class="accordion-toggle">
        <h3><span class="label label-number"><em>{{ $i }}</em></span> <?php _e('Profile image', 'municipio-intranet'); ?></h3>
    </span>
    <div class="accordion-content">
        <div class="grid">
            <div class="grid-md-12">
                <div class="form-group">
                    <div class="profile-image-upload">
                        @if (!empty(get_the_author_meta('user_profile_picture', get_current_user_id())))
                        <div class="profile-image profile-image-250 inline-block" style="background-image:url('{{ get_the_author_meta('user_profile_picture') }}');">
                            <button onclick="return confirm('<?php _e('Are your sure you want to remove the profile image?', 'municipio-intranet'); ?>');" formaction="?remove_profile_image=true" class="btn btn-icon btn-danger btn-sm text-lg" data-tooltip="<?php _e('Delete profile image', 'municipio-intranet'); ?>" data-tooltip-right>&times;</button>
                        </div>
                        @endif

                        <div class="image-upload inline-block" data-max-files="1" data-max-size="2000" data-preview-image="true" style="width:250px;height:250px;">
                            <div class="placeholder">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-picture-o fa-stack-2x"></i>
                                    <i class="fa fa-plus-circle fa-stack-1x"></i>
                                </span>
                                <div class="placeholder-text">
                                    <span class="placeholder-text-drag"><?php _e('Drag a photo here', 'municipio-intranet'); ?></span>
                                    <span class="placeholder-text-browse">
                                        <em class="placeholder-text-or"><?php _e('or', 'municipio-intranet'); ?></em>
                                        <label for="user_profile_image" class="btn btn-secondary btn-select-file"><?php _e('Select a photo', 'municipio-intranet'); ?></label>
                                    </span>
                                </div>
                            </div>
                            <div class="placeholder placeholder-is-dragover">
                                <span>Drop it like it's hot</span>
                            </div>
                            <div class="selected-file"></div>
                            <input type="file" id="user_profile_image" name="user_profile_image" class="hidden">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.modal.missing-data-fields.nav')
    </div>
</section>
