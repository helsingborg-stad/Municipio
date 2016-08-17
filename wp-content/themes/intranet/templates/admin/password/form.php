<div class="wrap" id="modularity-options">

    <h1><?php _e('Password reset instructions', 'municipio-intranet'); ?></h1>

    <form method="post">
        <input type="hidden" name="password-reset-action" value="save">

        <?php wp_nonce_field('password-reset'); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                <div id="post-body-content" style="display:none;">
                    <!-- #post-body-content -->
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="ui-sortable-handle"><?php _e('Save', 'municipio-intranet'); ?></h2>
                        <div class="inside">
                            <div id="major-publishing-actions" style="margin: -7px -12px -12px;width: calc(100% + 24px);">
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <input type="submit" value="<?php _e('Save', 'municipio-intranet'); ?>" class="button button-primary button-large" id="publish" name="publish">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle" style="cursor:default;"><?php _e('Instructions for password reset (html)', 'municipio-intranet'); ?></h2>
                        <div class="inside tag-manager-tags">
                            <textarea name="password-instructions" style="width: 100%;height: 400px;"><?php echo get_site_option('password-reset-instructions'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
