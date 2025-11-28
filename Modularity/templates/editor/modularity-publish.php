<p>
    <ul>
        <!-- Page -->
        <li>
            <strong><?php _e('Page:', 'modularity'); ?></strong>
            <?php echo \Modularity\Editor::$isEditing['title']; ?>
            <?php if (isset(\Modularity\Editor::$isEditing['id']) && !is_null(\Modularity\Editor::$isEditing['id'])) : ?>
                (ID: <?php echo \Modularity\Editor::$isEditing['id']; ?>)
            <?php endif; ?>
        </li>

        <!-- Template -->
        <li>
            <strong><?php _e('Using template:', 'modularity'); ?></strong>
            <?php echo \Modularity\Editor::$isEditing['template']; ?>
        </li>
    </ul>
</p>

<div id="major-publishing-actions" style="margin: 0px -12px -12px;">
    <div id="publishing-action">
        <span class="spinner"></span>
        <input type="submit" value="<?php _e('Save', 'modularity'); ?>" class="button button-primary button-large" id="publish" name="publish">
    </div>
    <div class="clear"></div>
</div>
