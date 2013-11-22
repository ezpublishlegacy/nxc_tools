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
$cacheBlockTimestamp = $Timestamps['template-block-cache'];
$viewCacheTimestamp = $Timestamps['content-view-cache'];

/*
Clearing expired templateblock cache with purge 
*/

$whereClause = "
    WHERE scope='template-block'
        AND mtime < $cacheBlockTimestamp
        AND mtime > 0";

$query = array(
    'count' => "SELECT count(*) as count FROM ezdbfile $whereClause",
    'purge' => "DELETE FROM ezdbfile $whereClause LIMIT $limit"
);

$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );

nxcDBFilePurge::printStats( $stats );

/*
Clearing expired view cache with purge 
*/

$whereClause = "
    WHERE scope='viewcache'
        AND mtime < $viewCacheTimestamp";

$query = array(
    'count' => "SELECT count(*) as count FROM ezdbfile $whereClause",
    'purge' => "DELETE FROM ezdbfile $whereClause LIMIT $limit"
);

$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );
nxcDBFilePurge::printStats( $stats );


?>
