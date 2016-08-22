<?php
$sites = get_field('incidents', $module->ID);
$level = get_field('incident_level', $module->ID);
$length = get_field('length', $module->ID);

$incidents = \Intranet\CustomPostType\Incidents::getIncidents($sites, $level, $length);
?>
<div class="<?php echo implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-index'), $module->post_type, $args)); ?>">
    <h4 class="box-title gutter gutter-sm" style="padding-bottom:0;"><?php echo $module->post_title ? $module->post_title : __('Incidents', 'municipio-intranet'); ?></h4>
    <div class="box-content">
        <ul class="list-item-spacing">
            <?php foreach ($incidents as $incident) : ?>
            <li><a href="<?php echo get_blog_permalink($incident->blog_id, $incident->ID); ?>" class="notice <?php echo $incident->incident_level; ?> pricon pricon-notice-<?php echo $incident->incident_level; ?> pricon-space-right"><?php echo $incident->post_title; ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php if (get_field('link_to_archive', $module->ID)) : ?>
    <div class="gutter gutter-sm gutter-top">
        <a href="#" class="read-more"><?php _e('Show all incidents', 'municipio-intranet'); ?></a>
    </div>
    <?php endif; ?>
</div>
