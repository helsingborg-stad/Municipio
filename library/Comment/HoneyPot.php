<?php

namespace Municipio\Comment;

class HoneyPot
{
    protected $field_content;
    protected $field_name;
    protected $field_min_time; //ms

    public function __construct()
    {
        //Not in admin
        if (is_admin()) {
            return;
        }

        //Verification values
        $this->field_content  = substr(md5(NONCE_SALT . NONCE_KEY), 5, 15);
        $this->field_name     = substr(md5(AUTH_KEY), 5, 15);
        $this->field_min_time = 4513;

        //Print frontend fields
        add_filter('comment_form_logged_in_after', array($this, 'addHoneyPotFieldFilled'));
        add_filter('comment_form_after_fields', array($this, 'addHoneyPotFieldFilled'));

        add_filter('comment_form_logged_in_after', array($this, 'addHoneyPotFieldBlank'));
        add_filter('comment_form_after_fields', array($this, 'addHoneyPotFieldBlank'));

        add_filter('comment_form_logged_in_after', array($this, 'addHoneyPotFieldTimer'));
        add_filter('comment_form_after_fields', array($this, 'addHoneyPotFieldTimer'));

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
    public function addHoneyPotFieldTimer()
    {
        echo $this->createField(
            $this->field_name . '_ti',
            $this->field_content,
            'hp-timer-field'
        );
        echo '
            <script type="text/javascript">
                ["onload"].forEach(function(e){
                    [].forEach.call(document.querySelectorAll(".hp-timer-field"), function(item) {
                        setTimeout(function() {
                            item.value = "' . $this->field_min_time . '"; 
                        }.bind(item), ' . $this->field_min_time . '); 
                    });
                });
            </script>
        ';
    }

    /**
     * Outputs honey pot filled field
     */
    public function addHoneyPotFieldFilled()
    {
        echo $this->createField(
            $this->field_name . '_fi',
            $this->field_content
        );
    }

    /**
     * Outputs honey pot blank field
     */
    public function addHoneyPotFieldBlank()
    {
        echo $this->createField($this->field_name . '_bl');
    }

    /**
     * Outputs HTML for a input field
     *
     * @param srting    $fieldName     The name parameter of the field
     * @param string    $fieldContent  The predefined value of the field
     * @return string                  The field
     */
    private function createField($fieldName, $fieldContent = "", $class = ""): string
    {
        return '
            <div class="fake-hide" aria-hidden="true">
                <input 
                    class="hidden ' . $class . '" 
                    name="' . $fieldName . '" 
                    type="text" 
                    value="' . $fieldContent . '" 
                    size="30" 
                    autocomplete="off" 
                    tabIndex="-1"
                    aria-hidden="true"
                >
            </div>
        ';
    }

    /**
     * Validate honeypot fields before saving comment
     * @param  array $data The comment data
     * @return array       Comment data or die
     */
    public function honeyPotValidateFieldContent($data)//:void
    {
        //Require these fields
        $lookForFields = [
            $this->field_name . '_fi',
            $this->field_name . '_bl',
            $this->field_name . '_ti'
        ];

        //Check that all fields exists
        foreach ($lookForFields as $field) {
            if (!array_key_exists($field, $_POST)) {
                wp_die(__("Could not verify that you are human (some form fields are missing).", 'municipio'));
            }
        }

        //Validate empty
        if ($_POST[$this->field_name . '_bl'] != '') {
            wp_die(__("Could not verify that you are human.", 'municipio') . " (bl)");
        }

        //Validate filled
        if ($_POST[$this->field_name . '_fi'] != $this->field_content) {
            wp_die(__("Could not verify that you are human.", 'municipio') . " (fi)");
        }

        //Validate timer
        if ($_POST[$this->field_name . '_ti'] != $this->field_min_time) {
            wp_die(__("Could not verify that you are human.", 'municipio') . " (ti)");
        }

        return $data;
    }
}
