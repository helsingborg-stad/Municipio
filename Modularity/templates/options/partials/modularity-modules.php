<p>
    <?php _e('Enable and/or disable modules.', 'modularity'); ?><br>
    <span style="font-style:italic;"><?php _e('Note: You will not lose any data from disabling a module. However the modules will not be displayed on your site.', 'modularity'); ?></span>
</p>

<?php if (count(\Modularity\ModuleManager::$deprecated) > 0) : ?>
<p>
    <span style="color:#ff0000"><?php _e('Deprecated modules', 'modularity'); ?>:</span><br>
    <?php _e('Some of your modules is set as deprecated. That means that the modules already created will still be available to display and edit, but the option to create new modules is disabled.', 'modularity'); ?>
</p>
<?php endif; ?>

<div class="modularity-table-metabox-wrapper">
    <table class="modularity-table">
        <thead>
            <th class="checkbox-wrapper"><?php _e('Enabled', 'modularity'); ?></th>
            <th><?php _e('Module', 'modularity'); ?></th>
            <th><?php _e('Module', 'modularity'); ?> ID</th>
            <th><?php _e('Description', 'modularity'); ?></th>
        </thead>
        <tbody>
        <?php foreach ($available as $id => $module) : ?>
            <tr>
                <td class="checkbox">
                    <input type="checkbox" name="<?php echo $this->getFieldName('enabled-modules', true); ?>" value="<?php echo $id; ?>" <?php checked(in_array($id, $enabled) ? 'on' : null, 'on', true); ?>>
                </td>
                <td><strong><?php echo $module['labels']['name']; ?></strong> <?php if (in_array($id, \Modularity\ModuleManager::$deprecated)) : ?><span class="modularity-deprecated" style="color:#ff0000;">(<?php _e('Deprecated', 'modularity'); ?>)</span><?php endif; ?></td>
                <td><span style="font-style:italic;"><?php echo $id; ?></span></td>
                <td><?php echo $module['description']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
