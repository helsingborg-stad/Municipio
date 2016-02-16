<?php
global $post;
$items = get_field('items', $module->ID);

$class = '';

switch ($args['id']) {
    case 'content-area':
        if (!is_front_page()) {
            $class = 'box-panel-secondary';
        }
        break;

    default:
        $class = 'box-panel-primary';
        break;
}

?>

<div class="box box-panel <?php echo $class; ?>">
    <h4 class="box-title"><?php echo $module->post_title; ?></h4>
    <ul>
        <?php foreach ($items as $item) : ?>
            <?php if ($item['type'] == 'external') : ?>
            <li>
                <a class="link-item link-item-outbound" href="<?php echo $item['link_external']; ?>" target="_blank"><?php echo $item['title'] ?></a>
            </li>
            <?php elseif ($item['type'] == 'internal') : ?>
            <li>
                <a class="link-item" href="<?php echo get_permalink($item['link_internal']->ID); ?>"><?php echo (!empty($item['title'])) ? $item['title'] : $item['link_internal']->post_title; ?>
                    <?php if ($item['date'] === true) : ?>
                    <span class="date pull-right text-sm text-dark-gray"><?php echo date('Y-m-d', strtotime($item['link_internal']->post_date)); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
