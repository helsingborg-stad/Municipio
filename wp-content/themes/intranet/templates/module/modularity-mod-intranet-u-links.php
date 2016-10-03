<div class="<?php echo implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $module->post_type, $args)); ?>">
    <h4 class="box-title">
        <?php _e('Your links', 'municipio-intranet'); ?>
        <?php if (is_user_logged_in()) : ?>
        <button type="button" class="btn btn-plain btn-sm pricon-space-right pricon pricon-edit" data-user-link-edit><?php _e('Edit', 'municipio-intranet'); ?></button>
        <?php endif; ?>
    </h4>
    <div class="box-content">
        <?php if (!empty(\Intranet\Module\UserLinks::getLinks())) : ?>
        <ul class="links">
            <?php foreach (\Intranet\Module\UserLinks::getLinks() as $link) : ?>
            <li>
                <a class="link-item link-item-light" href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a>
                <?php if (is_user_logged_in()) : ?>
                <button class="btn btn-icon btn-sm text-lg pull-right only-if-editing" data-user-link-remove="<?php echo $link['url']; ?>" type="button">&times;</button>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
            <?php _e('You have not added any links yet…', 'municipio-intranet'); ?>
        <?php endif; ?>
    </div>

    <?php if (is_user_logged_in()) : ?>
    <form action="<?php echo municipio_intranet_current_url(); ?>" class="only-if-editing" data-user-link-add>
        <h5><?php _e('Add new link', 'municipio-intranet'); ?></h5>
        <div class="form-group">
            <label for="user-link-title"><?php _e('Title', 'municipio-intranet'); ?></label>
            <input type="text" name="user-link-title" id="user-link-title">
        </div>
        <div class="form-group">
            <label for="user-link-url"><?php _e('Url', 'municipio-intranet'); ?></label>
            <input type="text" name="user-link-url" id="user-link-url">
        </div>
        <div class="form-group">
            <button type="submit" class="btn"><?php _e('Save'); ?></button>
        </div>
    </form>
    <?php endif; ?>
</div>
