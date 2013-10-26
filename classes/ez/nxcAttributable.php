<?php
/**
 * @author dl
 * @copyright Copyright (C) 2013 NXC AS.
 * @pakacge nxc_tools
 */

/**
 * Handles ez 'attribute' feature
 */
class nxcAttributable
{
    protected $_attributeList = array();

    /**
     * @return (array)
     */
    public function attributes()
    {
        return array_keys($this->_attributeList);
    }

    /**
     * @return (bool)
     */
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->_attributeList);
    }

    /**
     * @param (string)
     * @param (mixed)
     *
     * @return (void)
     */
    public function setAttribute($attr, $val)
    {
        if (!$this->hasAttribute($attr)) {
            eZDebug::writeError("no such attribute '$attr'", get_class($this).'::'.__FUNCTION__);
            return;
        }

        $this->_attributeList[$attr] = $val;
    }

    /**
     * @return (mixed|'') return empty string if no attribute $attr found
     */
    public function attribute($attr)
    {
        if (!$this->hasAttribute($attr)) {
            eZDebug::writeError("no such attribute '$attr'", get_class($this).'::'.__FUNCTION__);
            return '';
        }

        $value = $this->_attributeList[$attr];

        if (is_string($value) && strlen($value) && method_exists($this, $value)) {
            $value = $this->$value();
        }

        return $value;
    }

}

?>
