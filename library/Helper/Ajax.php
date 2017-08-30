<?php

namespace Municipio\Helper;

abstract class Ajax
{
    //Data array passed to localize function
    protected $data = array();

    //Default handle
    protected $handle = 'municipio';

    /**
     *  Localize - Pass data from PHP to JS
     *  @param  $var - Define variable name used in JS file
     *  @param  $handle - Specify script handle (optional)
     *  @return boolean
     */
    public function localize($var, $handle = null)
    {
        if(! isset($this->data) || ! $this->data || is_string($var) == false) {
            return false;
        }

        if($handle !== null) {
            $this->handle = $handle;
        }

        $this->var = $var;

        add_action( 'wp_enqueue_scripts', array($this, '_localize') );

        return true;
    }

    public function _localize()
    {
        wp_localize_script( $this->handle, $this->var, $this->data);
    }

    /**
     *  Hook - Hook function to WP ajax
     *  @param  string $functionName - Name of existing function within the class
     *  @param  boolean $private - Hook fires only for logged-in users, set to false for to fire for everyone
     *  @return boolean
     */
    public function hook($functionName, $private = true)
    {
        if(! method_exists($this, $functionName) || is_bool($private) == false) {
            return false;
        }

        switch ($private) {

            case false:
                add_action( 'wp_ajax_nopriv_'.$functionName, array($this,$functionName) );
            break;

            default:
                add_action( 'wp_ajax_'.$functionName, array($this,$functionName) );
            break;
        }

        add_action( 'wp_ajax_'.$functionName, array($this,$functionName) );

        return true;
    }
}
