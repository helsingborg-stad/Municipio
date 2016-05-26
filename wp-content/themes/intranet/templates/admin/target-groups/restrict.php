<?php global $post; $current = get_post_meta($post->ID, '_target_groups', true); ?>
<p>
    <?php foreach (\Intranet\User\TargetGroups::getAvailableGroups() as $group) : ?>
        <label class="target-group-restrict-checkbox">
            <input type="checkbox" name="target_groups[]" value="<?php echo $group->id; ?>" <?php checked(true, in_array($group->id, (array)$current), true); ?>> <?php echo $group->tag; ?>
        </label>
    <?php endforeach; ?>
</p>
