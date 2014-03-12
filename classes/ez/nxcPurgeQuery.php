<?php
/**
 * @author sp@nxc.no
 * @copyright Copyright (C) 2013 NXC AS. All rights reserved.
 *
 */

class nxcPurgeQuery implements ArrayAccess
{
    private static $queryCount = 1;
    private $timestamp;
    private $expiryValue;
    private $purgeQueryPattern;
    private $countQueryPattern;

    function __construct( $expiryValue, $purgeQueryPattern, $countQueryPattern )
    {
        $this->expiryValue = $expiryValue;
        $this->purgeQueryPattern = $purgeQueryPattern;
        $this->countQueryPattern = $countQueryPattern;
        $this->timestamp = $this->expiryValue->get();
    }


    public function offsetExists( $name )
    {
        if ( $name == 'purge' || $name == 'count' )
            return true;
        else
            return false;
    }

    public function offsetGet( $name )
    {
        if ( $name == 'purge' )
        {
            return $this->getPurgeQuery();
        }
        if ( $name == 'count' )
        {
            return  $this->getCountQuery();
        }
        return false;
    }

    function getPurgeQuery()
    {
        if ( self::$queryCount++ % 50  == 0 )
        {
           $oldTimestamp = $this->timestamp;
           $this->timestamp = $this->expiryValue->get();
           if ( $oldTimestamp !== $this->timestamp )
               echo "timestamp has changed from  $oldTimestamp to " . $this->timestamp;
        }
        $query = str_replace( '%timestampPattern%', $this->timestamp, $this->purgeQueryPattern );
//        echo "\n" . $query . "\n";
        return $query;
//str_replace( '%timestampPattern%', $this->timestamp, $this->purgeQueryPattern );
    }

    function getCountQuery()
    {
        return str_replace( '%timestampPattern%', $this->timestamp, $this->countQueryPattern );
    }

    public function offsetUnset( $offset )
    {
    }
    public function offsetSet($offset, $value)
    {
    }

}

?>
