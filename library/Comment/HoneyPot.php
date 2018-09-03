<?php

namespace Municipio\Comment;

class HoneyPot
{
    protected $field_content;
    protected $field_name;

    public function __construct()
    {
        //Not in admin
        if (is_admin()) {
            return;
        }

        //Verification values
        $this->field_content = substr(md5(NONCE_SALT . NONCE_KEY), 5, 15);
        $this->field_name = substr(md5(AUTH_KEY), 5, 15);

        //Print frontend fields
        add_filter('comment_form_logged_in_after', array($this, 'addHoneyPotFieldFilled'));
        add_filter('comment_form_after_fields', array($this, 'addHoneyPotFieldFilled'));

        add_filter('comment_form_logged_in_after', array($this, 'addHoneyPotFieldBlank'));
        add_filter('comment_form_after_fields', array($this, 'addHoneyPotFieldBlank'));

        //Catch fields
        add_filter('preprocess_comment', array($this, 'honeyPotValidateFieldContent'));

        //Add styling to hide field
        add_filter('comment_form', array($this, 'printFakeHideBox'));
    }

    /**
     * Outputs honey pot css
     */
    public function printFakeHideBox()
    {
        echo '<style>.fake-hide {width: 1px; height: 1px; opacity: 0.0001; position: absolute; overflow: hidden;}</style>';
    }

    /**
     * Outputs honey pot filled field
     */
    public function addHoneyPotFieldFilled()
    {
        echo '<div class="fake-hide"><input name="'.$this->field_name.'_fi" type="text" value="'.$this->field_content.'" size="30" autocomplete="off" tabIndex="-1"></div>';
    }

    /**
     * Outputs honey pot blank field
     */
    public function addHoneyPotFieldBlank()
    {
        echo '<div class="fake-hide"><input class="hidden" name="'.$this->field_name.'_bl" type="text" value="" size="30" autocomplete="off" tabIndex="-1"></div>';
    }

    /**
     * Validate honeypot fields before saving comment
     * @param  array $data The comment data
     * @return array       Comment data or die
     */
    public function honeyPotValidateFieldContent($data)
    {
        if (isset($_POST[$this->field_name.'_fi']) && isset($_POST[$this->field_name.'_bl'])) {
            if (empty($_POST[$this->field_name.'_bl']) && $_POST[$this->field_name.'_fi'] == $this->field_content) {
                return $data;
            }

            wp_die(__("Could not verify that you are human (some hidden form fields are manipulated).", 'municipio'));
        }

        wp_die(__("Could not verify that you are human (some form fields are missing).", 'municipio'));
    }
}
