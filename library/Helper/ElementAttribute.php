<?php

namespace Municipio\Helper;

class ElementAttribute
{
    public $attributes = array();
    public $classes = array();

    public function addClass($classes)
    {
        if (!is_string($classes) && !is_array($classes) || empty($classes)) {
            return false;
        }

        if (is_string($classes)) {
            $classes = array($classes);
        }

        foreach ($classes as $class)
        {
            $this->classes[] = $class;
        }
    }

    public function removeClass($classes)
    {
        if (!is_string($classes) && !is_array($classes) || empty($classes)) {
            return false;
        }

        if (is_string($classes)) {
            $classes = array($classes);
        }

        foreach ($classes as $class) {
            if (($key = array_search($class, $this->classes)) !== false) {
                unset($this->classes[$key]);
            }
        }
    }

    public function addAttribute($attributeName, $values)
    {
        if (!is_string($attributeName) || !is_string($values) && !is_array($values) || empty($attributeName) || empty($values)) {
            return false;
        }

        if (!isset($this->attributes[$attributeName])) {
            $this->attributes[$attributeName] = array();
        }

        if (is_string($values)) {
            $values = array($values);
        }

        foreach ($values as $value) {
            $this->attributes[$attributeName][] = $value;
        }
    }

    public function replaceClasses($classes)
    {
        if (!is_array($classes) || !is_string($classes) || empty($classes)) {
            return false;
        }

        if (is_string($classes)) {
            $classes = array($classes);
        }

        $this->classes = $classes;
    }

    public function getClasses()
    {
        if (isset($this->classes) && is_array($this->classes) && !empty($this->classes)) {
            return $this->classes;
        }
    }

    public function getAttributes($mergeClasses = true)
    {
        if ($mergeClasses && isset($this->classes) && is_array($this->classes) && !empty($this->classes)) {
            if (!isset($this->attributes['class'])) {
                $this->attributes = array_merge(array('class' => array()), $this->attributes);
            }
            $this->attributes['class'] = $this->classes;
        }

        if (isset($this->attributes) && is_array($this->attributes) && !empty($this->attributes)) {
            return $this->attributes;
        }
    }

    public function replaceAttributes($attributes)
    {
        if (!is_array($classes) || empty($classes)) {
            return false;
        }

        $this->attributes = $attributes;
    }

    public function outputAttributes($mergeClasses = true)
    {
        if ($mergeClasses && isset($this->classes) && is_array($this->classes) && !empty($this->classes)) {

            if (!isset($this->attributes['class'])) {
                $this->attributes = array_merge(array('class' => array()), $this->attributes);
            }
            $this->attributes['class'] = $this->classes;
        }

        if (!isset($this->attributes) || !is_array($this->attributes) || empty($this->attributes)) {
            return false;
        }

        return self::attributesToString($this->attributes);
    }

    /**
     * Turn an array of HTML attributes into a string
     * @param string $content String to get HTML attributes from
     * @return array       Attributes
     */
    public static function attributesToString($attributesArray)
    {
        if (!is_array($attributesArray) || empty($attributesArray)) {
            return false;
        }

        $attributes = array();

        foreach ($attributesArray as $attribute => $value) {
            if (!is_array($value) && !is_string($value) || !$value || empty($value)) {
                continue;
            }

            $values = (is_array($value)) ? implode(' ', array_unique($value)) : $value;
            $attributes[] = $attribute . '="' . $values . '"';
        }

        return implode(' ', $attributes);
    }
}
