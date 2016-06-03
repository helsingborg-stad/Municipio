<div class="<?php echo implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $module->post_type, $args)); ?>">
    <h4 class="box-title">
        <?php _e('Your systems', 'municipio-intranet'); ?>
        <?php if (is_user_logged_in()) : ?>
        <a href="#modal-select-systems" class="btn btn-plain btn-sm pull-right" data-user-systems-edit><i class="fa fa-edit"></i> <?php _e('Edit', 'municipio-intranet'); ?></a>
        <?php endif; ?>
    </h4>
    <div class="box-content">
        <ul class="links">
            <li><a href="#" class="link-item link-item-light">System</a></li>
        </ul>
    </div>
</div>

<div id="modal-select-systems" class="modal modal-backdrop-2 modal-medium" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a class="btn btn-close" href="#close"></a>
            <h2 class="modal-title"><?php _e('Select systems', 'municipio-intranet'); ?></h2>
        </div>
        <div class="modal-body">
            <article>
            </article>
        </div>
        <div class="modal-footer">
            <a href="#close" class="btn btn-close"><?php _e('Cancel', 'municipio-intranet'); ?></a>
            <button type="button" class="btn btn-primary"><?php _e('Save', 'municipio-intranet'); ?></button>
        </div>
    </div><!-- /.modal-content -->
    <a href="#close" class="backdrop"></a>
</div><!-- /.modal -->
