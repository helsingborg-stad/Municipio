<form method="post" action="{{ wp_login_url($_SERVER["REQUEST_URI"]) }}" class="login-form">
    <div class="form-group">
        <label for="login-username"><?php _e('Username'); ?></label>
        <input type="text" name="log" id="login-username">
    </div>
    <div class="form-group">
        <label for="login-password"><?php _e('Password'); ?></label>
        <input type="password" name="pwd" id="login-password">
    </div>
    <div class="form-group">
        <label class="checkbox">
            <input id="rememberme" type="checkbox" value="forever" name="rememberme">
            <?php _e('Remember Me'); ?>
        </label>
    </div>
    <div class="form-group">
        @if (!empty(get_site_option('password-reset-instructions')))
        <a href="#forgot-password"><?php _e('Forgot your password?', 'municipio-intranet'); ?></a><br>
        @endif

        <input type="submit" class="btn btn-primary" value="<?php _e('Login', 'municipio-intranet'); ?>">
    </div>
</form>
