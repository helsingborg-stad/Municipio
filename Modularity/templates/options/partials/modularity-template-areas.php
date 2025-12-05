<p>
    <?php _e('Enable and/or disable areas in a specific template.', 'modularity'); ?><br>
    <span style="font-style:italic;"><strong><?php _e('Pro tip:', 'modularity'); ?></strong> <?php _e('Shift click on a checkbox to check/uncheck the area in all templates.', 'modularity'); ?></span>
</p>

<div class="modularity-table-metabox-wrapper">
    <table class="modularity-table">
        <thead>
            <th><?php _e('Template', 'modularity'); ?></th>
            <th><?php _e('Areas', 'modularity'); ?></th>
        </thead>
        <tbody>
        <?php
        foreach ($templates as $name => $path) :
            $enabled = isset($modularityOptions['enabled-areas'][$path]) && is_array($modularityOptions['enabled-areas'][$path]) ? $modularityOptions['enabled-areas'][$path] : array();
        ?>
            <tr>
                <td><strong><?php echo ucwords($name); ?></strong></td>
                <td>
                    <?php
                        foreach ($wp_registered_sidebars as $sidebar) :
                            $checked = $enabled ? checked(in_array($sidebar['id'], $enabled) ? $sidebar['id'] : null, $sidebar['id'], false) : '';
                    ?>
                        <label style="display:block;padding: 3px 0;">
                            <input class="sidebar-area-activator" type="checkbox" name="modularity-options[enabled-areas][<?php echo $path; ?>][]" value="<?php echo $sidebar['id']; ?>" <?php echo $checked; ?>>
                            <?php echo $sidebar['name']; ?>
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
