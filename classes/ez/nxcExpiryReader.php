<?php
/**
 * @author sp@nxc.no
 * @copyright Copyright (C) 2013 NXC AS. All rights reserved.
 *
 */

class nxcExpiryReader
{
    private $expiryData;
    private $expiryFile;

    function __construct( $expiryFile )
    {
        $this->expiryFile = $expiryFile;
        $this->loadData();
    }

    function loadData()
    {
        $ini = eZINI::instance( 'nxc-tools.ini' );

        $cacheFileHandler = eZClusterFileHandler::instance( $this->expiryFile );
        if ( $cacheFileHandler->fileExists( $cacheFileHandler->filePath ) )
        {
             $expiryData =  $cacheFileHandler->fetch();
             include( $cacheFileHandler->filePath );

        }
        $this->expiryData = $Timestamps;
    }

    function __get( $name )
    {
        $this->loadData();
        if ( array_key_exists($name, $this->expiryData ) )
        {
            return $this->expiryData[$name];
        }
        return null;
    }

    function __isset( $name )
    {
        return isset($this->expiryData[$name]);
    }
}


?>
