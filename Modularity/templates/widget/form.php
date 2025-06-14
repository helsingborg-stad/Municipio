<p style="border: 1px solid #ddd; background: #f9f9f9; padding: 10px 15px;">
    <strong><?php _e('Selected module', 'modularity'); ?></strong><br>

    ID:<span class="modularity-widget-module-id-span"><?php echo isset($instance['module_id']) && !empty($instance['module_id']) ? $instance['module_id'] : '<i>' . __('Not selected', 'modularity') . '</i>'; ?></span>
    <input type="hidden" class="modularity-widget-module-id" name="<?php echo $this->get_field_name('module_id'); ?>" id="<?php echo $this->get_field_id('module_id'); ?>" value="<?php echo isset($instance['module_id']) && !empty($instance['module_id']) ? $instance['module_id'] : ''; ?>"><br>

    Module title: <span class="modularity-widget-module-title-span"><?php echo isset($instance['title']) && !empty($instance['title']) ? $instance['title'] : '<i>' . __('Not selected', 'modularity') . '</i>'; ?></span>
    <input type="hidden" class="modularity-widget-module-title" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo isset($instance['title']) && !empty($instance['title']) ? $instance['title'] : ''; ?>">
</p>
<p class="modularity-widget-module-type">
    <label for="<?php echo $this->get_field_id('module_type'); ?>"><?php _e('Module type', 'modularity'); ?>:</label>
    <select class="widefat" name="<?php echo $this->get_field_name('module_type'); ?>" id="<?php echo $this->get_field_id('parent'); ?>">
        <?php foreach ($moduleTypes as $key => $type) : ?>
            <option value="<?php echo $key; ?>" <?php selected(isset($instance['module_type']) ? $instance['module_type'] : '', $key, true); ?>><?php echo $type['labels']['name']; ?></option>
        <?php endforeach; ?>
    </select>
</p>
<p class="modularity-widget-module-size">
    <label for="<?php echo $this->get_field_id('module_size'); ?>"><?php _e('Module width', 'modularity'); ?>:</label>
    <select class="widefat" name="<?php echo $this->get_field_name('module_size'); ?>" id="<?php echo $this->get_field_id('size'); ?>">
        <?php foreach (\Modularity\Editor::widthOptions() as $key => $type) : ?>
            <option value="<?php echo $key; ?>" <?php selected(isset($instance['module_size']) ? $instance['module_size'] : '', $key, true); ?>><?php echo $type; ?></option>
        <?php endforeach; ?>
    </select>
</p>

<p class="modularity-widget-module-import">
    <a href="#" class="button modularity-js-thickbox-widget-import-widget"><?php _e('Browse modules', 'modularity'); ?></a>

    <a href="post.php?post=<?php echo isset($instance['module_id']) && !empty($instance['module_id']) ? $instance['module_id'] : ''; ?>&action=edit&is_thickbox=true" class="button modularity-js-thickbox-open-widget modularity-widget-module-edit <?php echo isset($instance['module_id']) && !empty($instance['module_id']) ? '' : 'hidden'; ?>"><?php _e('Edit module', 'modularity'); ?></a>
</p>
