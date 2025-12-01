<div class="wrap" id="modularity-options">

    <h1><?php esc_html_e(get_admin_page_title(), 'modularity'); do_action('Modularity/options_page_title_suffix'); ?></h1>

    <form name="my_form" method="post">
        <input type="hidden" name="modularity-action" value="modularity-options">
        <input type="hidden" name="<?php echo \Modularity\Editor::EDITOR_PAGE_LOADING_KEY ?>" value="1">

        <?php
        wp_nonce_field('modularity-options');

        /* Used to save closed meta boxes and their order */
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);

        do_action('edit_form_top');
        ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                <div id="post-body-content" style="display:none;">
                    <!-- #post-body-content -->
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <?php do_meta_boxes('', 'side', null); ?>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <?php do_meta_boxes('', 'normal', null); ?>
                    <?php do_meta_boxes('', 'advanced', null); ?>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function () {
        if (typeof postboxes !== 'undefined') {
            postboxes.add_postbox_toggles(pagenow);
        }
    });
</script>
