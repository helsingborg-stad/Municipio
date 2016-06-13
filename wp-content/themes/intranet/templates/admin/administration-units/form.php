<div class="wrap" id="modularity-options">

    <h1><?php _e('Administration units', 'municipio-intranet'); ?></h1>

    <form method="post">
        <input type="hidden" name="administration-units-action" value="save">

        <?php wp_nonce_field('manage-target-tags'); ?>

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
                        <h2 class="hndle ui-sortable-handle" style="cursor:default;"><?php _e('Administration units', 'municipio-intranet'); ?></h2>
                        <div class="inside tag-manager-tags">
                            <?php foreach (\Intranet\User\AdministrationUnits::getAdministrationUnits() as $unit) : ?>
                                <div class="tag-manager-tag">
                                    <?php echo $unit->name; ?>
                                    <div class="tag-manager-actions">
                                        <button type="submit" class="btn-plain municipio-delete" name="administration-unit-delete" value="<?php echo $unit->id; ?>" onclick="return confirm('<?php _e('Do you want to permanently remove the administration unit?', 'municipio-intranet'); ?>')"><span class="dashicons dashicons-trash"></span></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle" style="cursor:default;"><?php _e('Add administration unit', 'municipio-intranet'); ?></h2>
                        <div class="inside">
                            <p>
                                <p>
                                    <label for=""><?php _e('Administration unit name', 'municipio-intranet'); ?></label>
                                    <input type="text" class="widefat" name="administration-unit-name">
                                </p>
                                <p>
                                    <input type="submit" class="button button-primary" name="administration-unit-add" value="<?php _e('Add', 'municipio-intranet'); ?>">
                                </p>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
