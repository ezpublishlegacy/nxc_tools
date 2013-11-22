<?php
/**
 * @author dl@nxc.no
 * @copyright Copyright (C) 2013 NXC AS. All rights reserved.
 *
 */

/**
 * Purge outdated template-block caches.
 */

$limit = 500;
#$ttl = 24 * 3600; // 24h
$sleepSeconds = 2;

nxcDBFilePurge::cliOutput(
    'Script options: ttl = '.(int)$ttl
          .', limit = '.(int)$limit
          .', sleep = '.(int)$sleepSeconds
);
$ini = eZINI::instance( 'nxc-tools.ini' );
$expiryFile = $ini->variable( 'CacheClearSettings', 'ExpiryFile');

$cacheFileHandler = eZClusterFileHandler::instance( $expiryFile );
if ( $cacheFileHandler->fileExists( $cacheFileHandler->filePath ) )
{
     $expiryData =  $cacheFileHandler->fetch();
    include( $cacheFileHandler->filePath );

}
//var_dump( $Timestamps );die();
$cacheTimestamp = $Timestamps['user-info-cache'];


/*
Clearing expired templateblock cache with purge 
*/

$whereClause = "
    WHERE scope='user-info-cache'
        AND mtime < $cacheTimestamp";

$query = array(
    'count' => "SELECT count(*) as count FROM ezdbfile $whereClause",
    'purge' => "DELETE FROM ezdbfile $whereClause LIMIT $limit"
);

$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );

nxcDBFilePurge::printStats( $stats );


?>
