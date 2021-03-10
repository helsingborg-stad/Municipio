<?php

$reCaptcha = (!is_user_logged_in()) ? '<input type="hidden" class="g-recaptcha-response"
                                                      name="g-recaptcha-response" value="" />' : '';
$reCaptchaTerms = (!is_user_logged_in()) ? __('This site is protected by reCAPTCHA and the Google
                                <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                                <a href="https://policies.google.com/terms">Terms of Service</a> apply.', 'municipio') : '';

    ob_start();
    comment_form(array(
        'class_submit' 			=> 'btn btn-primary',
    	'submit_button' 		=> $reCaptcha . '<p class="text-sm text-dark-gray">'.$reCaptchaTerms.'</p><div class="form-group"><input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" /></div>'
    ));
    echo str_replace('class="comment-respond"','class="comment-respond comment-respond-new u-mt-4"',ob_get_clean());
    
?>
