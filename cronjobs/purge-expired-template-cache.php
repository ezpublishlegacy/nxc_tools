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
$sleepSeconds = 1;

nxcDBFilePurge::cliOutput(
    'Script options: ttl = '.(int)$ttl
          .', limit = '.(int)$limit
          .', sleep = '.(int)$sleepSeconds
);

/*
Clearing expired templateblock cache with purge 
*/
$cacheDir = eZSys::cacheDirectory();
$whereClause = "
    WHERE name like \"". $cacheDir. "/template-block/%\"
        AND mtime < %timestampPattern%";


$ini = eZINI::instance( 'nxc-tools.ini' );
$expiryFile = $ini->variable( 'CacheClearSettings', 'ExpiryFile');
$reader = new nxcExpiryReader( $cacheDir . '/' . $expiryFile );
$timestampObject = new nxcExpiryValue( $reader, 'template-block-cache' );


$query = new nxcPurgeQuery( $timestampObject,
                         "DELETE FROM ezdbfile $whereClause LIMIT $limit",
                         "SELECT count(*) as count FROM ezdbfile $whereClause" );
//var_dump( $query );

nxcDBFilePurge::cliOutput(
    'Script queries are :'
          . $query['count']
    . "\n" . $query['purge']
);


$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );

nxcDBFilePurge::printStats( $stats );



/*
Clearing expired view cache with purge
*/


$timestampObject = new nxcExpiryValue( $reader, 'content-view-cache' );

$whereClause = "
    WHERE scope='viewcache'
        AND mtime < %timestampPattern%";

$query = new nxcPurgeQuery( $timestampObject,
                         "DELETE FROM ezdbfile $whereClause LIMIT $limit",
                         "SELECT count(*) as count FROM ezdbfile $whereClause" );

nxcDBFilePurge::cliOutput(
    'Script queries are :'
          . $query['count']
     . "\n" . $query['purge']
);

$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );

nxcDBFilePurge::printStats( $stats );

?>
