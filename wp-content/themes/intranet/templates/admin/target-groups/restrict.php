<?php global $post; $current = get_post_meta($post->ID, '_target_groups', true); ?>
<p>
    <label class="target-group-restrict-checkbox">
        <input type="checkbox" name="target_groups[]" value="citrix" <?php checked(true, in_array('citrix', (array)$current), true); ?>> <?php _e('Logged in via Citrix'); ?>
    </label>
</p>
<p>
    <?php foreach (\Intranet\User\TargetGroups::getAvailableGroups() as $group) : ?>
        <label class="target-group-restrict-checkbox">
            <input type="checkbox" name="target_groups[]" value="<?php echo $group->id; ?>" <?php checked(true, in_array($group->id, (array)$current), true); ?>> <?php echo $group->tag; ?> <?php echo isset($group->administration_unit) && \Intranet\User\AdministrationUnits::getAdministrationUnit($group->administration_unit) ? '(' . \Intranet\User\AdministrationUnits::getAdministrationUnit($group->administration_unit) . ')' : '(' . __('All') . ')'; ?>
        </label>
    <?php endforeach; ?>
</p>
