<div class="grid-xs-12">
    <div class="box box-filled">
        <h4 class="box-title"><?php _e('Sync from Facebook', 'municipio-intranet'); ?></h4>
        <div class="box-content">
            <p>
                <?php _e('We can sync some of your profile settings from Facebook. If you would like to do this click the login button below to login to Facebook and allow us to get needed information.', 'municipio-intranet'); ?>
            </p>

            <div class="fb-login-container gutter gutter-top">
                <div class="fb-login-button" data-max-rows="1" data-size="large" data-show-faces="false" data-auto-logout-link="false" data-default-audience="only_me" data-scope="public_profile, user_birthday, user_location" onlogin="facebookProfileSync"></div>
            </div>
        </div>
    </div>
</div>
