<ul
    class="modularity-sidebar-area modularity-js-droppable modularity-js-sortable modularity-spinner"
    data-empty="<?php _e('Drag your modules here…', 'modularity'); ?>"
    data-area-id="<?php echo $args['args']['sidebar']['id']; ?>"
></ul>

<div class="modularity-sidebar-options">
    <div class="container">
        <div class="col">
            <?php _e('Show modules', 'modularity'); ?>
            <select name="modularity_sidebar_options[<?php echo $args['args']['sidebar']['id']; ?>][hook]">
                <option value="before" <?php selected('before', isset($options['hook']) ? $options['hook'] : '', true); ?>><?php _e('before', 'modularity'); ?></option>
                <option value="after" <?php selected('after', isset($options['hook']) ? $options['hook'] : '', true); ?>><?php _e('after', 'modularity'); ?></option>
            </select>
            widgets
        </div>
        <div class="col">
            <label>
                <input type="checkbox" value="true" name="modularity_sidebar_options[<?php echo $args['args']['sidebar']['id']; ?>][hide_widgets]" <?php checked(true, isset($options['hide_widgets']), true); ?>>
                <?php _e('Hide global widgets', 'modularity'); ?>
            </label>
        </div>
    </div>
</div>
