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
$ttl = 24 * 3600; // 24h
$sleepSeconds = 2;

nxcDBFilePurge::cliOutput(
    'Script options: ttl = '.(int)$ttl
          .', limit = '.(int)$limit
          .', sleep = '.(int)$sleepSeconds
);

$whereClause = "
    WHERE scope='template-block'
        AND mtime < unix_timestamp(now() - interval $ttl second)
        AND mtime > 0";

$query = array(
    'count' => "SELECT count(*) as count FROM ezdbfile $whereClause",
    'purge' => "DELETE FROM ezdbfile $whereClause LIMIT $limit"
);

$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );

nxcDBFilePurge::printStats( $stats );

?>
