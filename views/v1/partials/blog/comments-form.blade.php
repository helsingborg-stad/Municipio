<?php
$key = defined('G_RECAPTCHA_KEY') ? G_RECAPTCHA_KEY : '';
$reCaptcha = (!is_user_logged_in(
    0)) ? '<div class="form-group"><div class="g-recaptcha" data-sitekey="' . $key . '"></div></div>' : '';

ob_start();
comment_form(array(
    'class_submit' 			=> 'btn btn-primary',
    'submit_button' 		=> $reCaptcha . '<div class="form-group"><input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" /></div>'
));
echo str_replace('class="comment-respond"','class="comment-respond comment-respond-new u-mt-4"',ob_get_clean());
?>