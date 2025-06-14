<p>
    <?php _e('Select which post types you would like to enable Modularity on.', 'modularity'); ?>
</p>

<div class="modularity-table-metabox-wrapper">
    <table class="modularity-table">
        <thead>
            <th class="checkbox-wrapper"><?php _e('Enabled', 'modularity'); ?></th>
            <th><?php _e('Post type', 'modularity'); ?></th>
        </thead>
        <tbody>
        <?php foreach ($postTypes as $postType) : ?>
            <tr>
                <td class="checkbox"><input type="checkbox" name="<?php echo $this->getFieldName('enabled-post-types', true); ?>" value="<?php echo $postType; ?>" <?php checked(in_array($postType, $enabled) ? 'on' : null, 'on', true); ?>></td>
                <td><strong><?php echo $postType; ?></strong></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
