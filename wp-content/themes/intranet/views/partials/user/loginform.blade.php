<form method="post" action="{{ wp_login_url($_SERVER["REQUEST_URI"]) }}">
    <div class="form-group">
        <label for="login-username"><?php _e('Username'); ?></label>
        <input type="text" name="log" id="login-username">
    </div>
    <div class="form-group">
        <label for="login-password"><?php _e('Password'); ?></label>
        <input type="password" name="pwd" id="login-password">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="<?php _e('Login', 'municipio-intranet'); ?>">
    </div>
</form>
