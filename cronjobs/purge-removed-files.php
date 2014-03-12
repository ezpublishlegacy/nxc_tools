<?php
/**
 * @author dl@nxc.no
 * @copyright Copyright (C) 2013 NXC AS. All rights reserved.
 *
 */

/**
 * Purge removed files.
 */

$limit = 500;
#$ttl = 24 * 3600; // 24h
$sleepSeconds = 1;

nxcDBFilePurge::cliOutput(
    'Script is puriging all removed  files from db cluster options are:'
          .', limit = '.(int)$limit
          .', sleep = '.(int)$sleepSeconds
);

$whereClause = "
    WHERE expired = 1
        AND mtime < 0";
//        AND mtime > 0";

$query = array(
    'count' => "SELECT count(*) as count FROM ezdbfile $whereClause",
    'purge' => "DELETE FROM ezdbfile $whereClause LIMIT $limit"
);

$stats = nxcDBFilePurge::execPurgeQuery( $query, $sleepSeconds, 'nxcDBFilePurge::printProgress' );

nxcDBFilePurge::printStats( $stats );



?>
