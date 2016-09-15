<?php
$sites = get_field('incidents', $module->ID);
$level = get_field('incident_level', $module->ID);
$length = get_field('length', $module->ID);

$incidents = \Intranet\CustomPostType\Incidents::getIncidents($sites, $level, $length);
?>
<div class="box box-plain">
    <ul class="list-item-spacing">
        <?php foreach ($incidents as $incident) : ?>
        <li><a href="<?php echo get_blog_permalink($incident->blog_id, $incident->ID); ?>" class="notice <?php echo $incident->incident_level; ?> pricon pricon-notice-<?php echo $incident->incident_level; ?> pricon-space-right"><?php echo $incident->post_title; ?></a></li>
        <?php endforeach; ?>
    </ul>

    <?php if (get_field('link_to_archive', $module->ID)) : ?>
        <div class="">
            <a href="<?php echo get_post_type_archive_link('incidents'); ?>" class="pricon pricon-plus-o pricon-space-right text-sm"><?php _e('Show all incidents', 'municipio-intranet'); ?></a>
        </div>
    <?php endif; ?>
</div>

