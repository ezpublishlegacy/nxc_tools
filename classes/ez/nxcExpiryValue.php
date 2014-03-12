<?php
/**
 * @author sp@nxc.no
 * @copyright Copyright (C) 2013 NXC AS. All rights reserved.
 *
 */

class nxcExpiryValue
{
    private $expiryReader;
    private $valueKey;
    function __construct( $reader, $valueKey )
    {
        $this->expiryReader = $reader;
        $this->valueKey = $valueKey;
    }

    function get()
    {
        $ts = $this->expiryReader->{$this->valueKey};
//        echo "read timestamp: " . $ts . "\n";
        return $ts;
//        return $this->expiryReader->{$this->valueKey};
    }
}

class nxcExpiryValueTest
{
    function __construct( $reader, $valueKey )
    {
    }

    function get()
    {
        return time() - 7200;
//        return $this->expiryReader->{$this->valueKey};
    }
}

?>
