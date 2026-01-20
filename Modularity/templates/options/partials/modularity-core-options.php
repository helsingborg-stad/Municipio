<?php global $modularityOptions; ?>
<div class="modularity-form-group">
    <ul>
        <li>
            <label>
                <input type="checkbox" name="<?php echo $this->getFieldName('show-modules-in-menu'); ?>" value="on" <?php checked(isset($modularityOptions['show-modules-in-menu']) ? $modularityOptions['show-modules-in-menu'] : null, 'on', true); ?>>
                <?php _e('Show module\'s post type in admin menu', 'modularity'); ?>
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="<?php echo $this->getFieldName('show-modules-usage-in-post-list'); ?>" value="on" <?php checked(isset($modularityOptions['show-modules-usage-in-post-list']) ? $modularityOptions['show-modules-usage-in-post-list'] : null, 'on', true); ?>>
                <?php _e('Show module usage in post module list', 'modularity'); ?>
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="<?php echo $this->getFieldName('show-modules-usage-edit-notice-nag'); ?>" value="on" <?php checked(isset($modularityOptions['show-modules-usage-edit-notice-nag']) ? $modularityOptions['show-modules-usage-edit-notice-nag'] : null, 'on', true); ?>>
                <?php _e('Show notice nag in module edit when it is used in several places', 'modularity'); ?>
            </label>
        </li>
        <li>
            <label>
                <input type="checkbox" name="<?php echo $this->getFieldName('show-modules-usage-in-frontend'); ?>" value="on" <?php checked(isset($modularityOptions['show-modules-usage-in-frontend']) ? $modularityOptions['show-modules-usage-in-frontend'] : null, 'on', true); ?>>
                <?php _e('Show module usage in front end', 'modularity'); ?>
            </label>
        </li>
    </ul>
</div>