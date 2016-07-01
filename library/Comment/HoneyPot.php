<?php

namespace Municipio\Comment;

class HoneyPot
{

    protected $field_content;
    protected $field_name;

    public function __construct()
    {

        //Verification values
        $this->field_content = substr(md5(NONCE_SALT+NONCE_KEY), 5, 15);
        $this->field_name = substr(md5(AUTH_KEY), 5, 15);

        //Print frontend fields
        add_filter('comment_form_default_fields', array($this, 'addHoneyPotFieldFilled'));
        add_filter('comment_form_default_fields', array($this, 'addHoneyPotFieldBlank'));

        //Cactch fields
        add_filter('preprocess_comment', array($this, 'honeyPotValidateFieldContent'));
    }

    public function addHoneyPotFieldFilled($fields)
    {
        if (is_array($fields)) {
            $fields = array(
                md5($this->field_name.'_fi') => '<input class="hidden" name="'.$this->field_name.'_fi" type="text" value="'.$this->field_content.'" size="30"/>'
            ) + $fields;
        }
        return $fields;
    }

    public function addHoneyPotFieldBlank($fields)
    {
        if (is_array($fields)) {
            $fields = array(
                md5($this->field_name.'_bl') => '<input class="hidden" name="'.$this->field_name.'_bl" type="text" value="" size="30"/>'
            ) + $fields;
        }
        return $fields;
    }

    public function honeyPotValidateFieldContent($data)
    {
        if (isset($_POST[$this->field_name.'_fi']) && isset($_POST[$this->field_name.'_bl'])) {
            if (empty($_POST[$this->field_name.'_bl']) && $_POST[$this->field_name.'_fi'] == $this->field_content) {
                return $data;
            } else {
                wp_die(__("Could not verify that you are human (some hidden form fields are manipulated).", 'municipio'));
            }
        } else {
            wp_die(__("Could not verify that you are human (some form fields are missing).", 'municipio'));
        }
    }
}
