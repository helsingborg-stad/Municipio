<?php

namespace Municipio\Helper;

/**
 * Class ElementAttribute
 * Helper class for managing HTML element attributes and classes.
 */
class ElementAttribute
{
    public $attributes = array();
    public $classes    = array();

     /**
     * Add one or more classes to the element.
     *
     * @param string|array $classes The class or classes to add.
     * @return bool Returns true on success, false on failure.
     */
    public function addClass($classes)
    {
        if (!is_string($classes) && !is_array($classes) || empty($classes)) {
            return false;
        }

        if (is_string($classes)) {
            $classes = array($classes);
        }

        foreach ($classes as $class) {
            if (empty($class)) {
                continue;
            }

            $this->classes[] = $class;
        }
    }

    /**
     * Remove one or more classes from the element.
     *
     * @param string|array $classes The class or classes to remove.
     * @return bool Returns true on success, false on failure.
     */
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


    /**
     * Add one or more attributes to the element.
     *
     * @param string|array $attributes The attribute or attributes to add.
     * @param string|array $values The value or values for the attribute.
     * @return bool Returns true on success, false on failure.
     */
    public function addAttribute($attributes, $values = '')
    {
        if (!$this->checkValidAttributeValues($attributes, $values)) {
            return false;
        }

        if (!is_array($attributes)) {
            $attributes = array($attributes => $values);
        }

        foreach ($attributes as $attributeKey => $attributeValues) {
            $this->newAttribute($attributeKey, $attributeValues);
        }
    }

    /**
     * Check that attributes and values are valid
     *
     * @param mixed $attributes The attribute or attributes to add.
     * @param mixed $values The value or values for the attribute.
     * @return bool Returns true on success, false on failure.
     */
    private function checkValidAttributeValues($attributes, $values): bool
    {
        if (!is_string($attributes) && !is_array($attributes) || empty($attributes)) {
            return false;
        }

        if (!is_string($values) && !is_array($values) || is_string($attributes) && empty($values)) {
            return false;
        }

        // If both conditions are met, return true
        return true;
    }

     /**
     * Add a new attribute with values to the element.
     *
     * @param string $attribute The attribute to add.
     * @param string|array $values The value or values for the attribute.
     */
    public function newAttribute($attribute = '', $values = '')
    {
        if (!is_string($values) && !is_array($values) || empty($attribute)) {
            return false;
        }

        if (!isset($this->attributes[$attribute])) {
            $this->attributes[$attribute] = array();
        }

        if (is_string($values)) {
            $values = array($values);
        }

        foreach ($values as $value) {
            $this->attributes[$attribute][] = $value;
        }
    }

    /**
     * Get the classes currently set for the element.
     *
     * @return array|null Returns an array of classes or null if none are set.
     */
    public function getClasses()
    {
        if (isset($this->classes) && is_array($this->classes) && !empty($this->classes)) {
            return $this->classes;
        }
    }

    /**
     * Get the attributes for the element.
     *
     * @param bool $mergeClasses Whether to merge classes into the attributes.
     * @return array|null Returns an array of attributes or null if none are set.
     */
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

    /**
     * Output the attributes as a string.
     *
     * @param bool $mergeClasses Whether to merge classes into the attributes.
     * @return string|bool Returns the string representation of attributes or false if none are set.
     */
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
     *
     * @param array $attributesArray Array to get HTML attributes from
     * @return string       Attributes
     */
    public static function attributesToString($attributesArray)
    {
        if (!is_array($attributesArray) || empty($attributesArray)) {
            return false;
        }

        $attributes = array();

        foreach ($attributesArray as $attribute => $value) {
            if (!is_array($value) && !is_string($value) || empty($value) || empty($attribute)) {
                continue;
            }

            $values       = (is_array($value)) ? implode(' ', array_unique($value)) : $value;
            $attributes[] = $attribute . '="' . $values . '"';
        }

        return implode(' ', $attributes);
    }
}
