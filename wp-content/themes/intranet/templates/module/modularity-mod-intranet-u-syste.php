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

<div id="modal-select-systems" class="modal modal-backdrop-2 modal-small" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <a class="btn btn-close" href="#close"></a>
            <h2 class="modal-title"><?php _e('Select systems', 'municipio-intranet'); ?></h2>
        </div>
        <div class="modal-body">
            <article>
                <form action="" method="post">
                    <p>
                        <?php _e('Select the systems that you would like to show in your "My systems" area.', 'municipio-intranet'); ?>
                    </p>

                    <p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php _e('Name', 'municipio-intranet'); ?></th>
                                    <th><?php _e('Description', 'municipio-intranet'); ?></th>
                                    <th><?php _e('Url', 'municipio-intranet'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (\Intranet\User\Systems::getAvailabelSystems('user', array('selectable', 'forced')) as $system) : ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" name="system-selected[]" value="<?php echo $system->id; ?>"></td>
                                    <td><?php echo $system->name; ?></td>
                                    <td><?php echo $system->description; ?></td>
                                    <td><?php echo $system->url; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </p>
                </form>
            </article>
        </div>
        <div class="modal-footer">
            <a href="#close" class="btn btn-close"><?php _e('Cancel', 'municipio-intranet'); ?></a>
            <button type="button" class="btn btn-primary"><?php _e('Save', 'municipio-intranet'); ?></button>
        </div>
    </div><!-- /.modal-content -->
    <a href="#close" class="backdrop"></a>
</div><!-- /.modal -->
