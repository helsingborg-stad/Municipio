<?php if (!is_user_logged_in()) : ?>
<div class="box box-filled">
    <h4 class="box-title">Logga in</h4>
    <div class="box-content">
        <form method="post" action="<?php echo wp_login_url(home_url()); ?>">
            <input type="hidden" name="modularity-mod-login" value="true">

            <?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
                <div class="gutter gutter-bottom">
                <div class="notice"><?php _e('Login failed. Please make sure to enter username and password correctly.', 'municipio-intranet'); ?></div>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="login-username"><?php _e('Username'); ?></label>
                <input type="text" name="log" id="login-username">
            </div>
            <div class="form-group">
                <label for="login-password"><?php _e('Password'); ?></label>
                <input type="password" name="pwd" id="login-password">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="<?php _e('Log in'); ?>">
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
