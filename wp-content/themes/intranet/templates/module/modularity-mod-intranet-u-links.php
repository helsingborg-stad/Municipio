<div class="<?php echo implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $module->post_type, $args)); ?>">
    <h4 class="box-title">
        <?php _e('Your links', 'municipio-intranet'); ?>
        <button class="btn btn-plain btn-sm pull-right" data-user-link-edit><i class="fa fa-edit"></i> Edit</button>
    </h4>
    <div class="box-content">
        <?php if (!empty(\Intranet\Module\UserLinks::getLinks())) : ?>
        <ul class="links">
            <?php foreach (\Intranet\Module\UserLinks::getLinks() as $link) : ?>
            <li>
                <a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a>
                <button class="btn btn-icon btn-sm text-lg pull-right only-if-editing" data-user-link-remove="<?php echo $link['url']; ?>">&times;</button>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
            <?php _e('You have not added any links yet…', 'municipio-intranet'); ?>
        <?php endif; ?>
    </div>

    <form action="" class="only-if-editing" data-user-link-add>
        <h5><?php _e('Add new link', 'municipio-intranet'); ?></h5>
        <div class="form-group">
            <label for="user-link-title">Title</label>
            <input type="text" name="user-link-title" id="user-link-title">
        </div>
        <div class="form-group">
            <label for="user-link-url">Url</label>
            <input type="text" name="user-link-url" id="user-link-url">
        </div>
        <div class="form-group">
            <input type="submit" class="btn" value="<?php _e('Save'); ?>">
        </div>
    </form>
</div>
