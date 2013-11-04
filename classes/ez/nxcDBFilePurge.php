<?php
/**
 * @author dl@nxc.no
 * @copyright Copyright (C) 2013 NXC AS. All rights reserved.
 *
 */

/**
 * Purge files stored in DB, f.ex. caches
 */
class nxcDBFilePurge
{
    /**
     * Purge files with direct SQLs
     *
     * @param (array) sql queries: array( 'count' => SELECT query to get total files caunt
     *                                    'purge' => DELETE query to do actual remove )
     * @param (int) seconds to sleep between iterations
     * @param (callable) callback to call between iterations
     *
     * @return (array) statistics about purged caches
     */
    public static function execPurgeQuery( $query, $sleepSeconds, $callback )
    {
        $stats = array(
            'totalCount' => 0,
            'purgedCount' => 0,
            'elapsedSeconds' => 0,
            'updatesCount' => 0
        );

        $stime = microtime( true );
        $sleepSeconds = (int)$sleepSeconds;
        $purgedCount = 0;

        $isCluster = eZClusterFileHandler::instance() instanceof eZDBFileHandler;
        if ( $isCluster ) {
            $db = eZDB::instance();
	    $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );


            $rows = $db->arrayQuery( $query['count'] );
            $stats['totalCount'] = (int)$rows[0]['count'];

            $rowsCount = true;
            while ( $rowsCount ) {
		try {
            	    $db->query( $query['purge'] );
	            $rowsCount = ($db instanceof eZMySQLiDB) ? mysqli_affected_rows( $db->DBWriteConnection ) : mysql_affected_rows( $db->DBWriteConnection );
    	            $purgedCount += $rowsCount;
                    $stats['purgedCount'] = $purgedCount;
                    $stats['elapsedSeconds'] = round( microtime( true ) - $stime, 6 );
                    $stats['updatesCount'] += 1;
		}catch ( Exception $exception) {
  	    	    nxcDBFilePurge::cliOutput( print_r( $exception, true ) );
                    $rowsCount = true;
            	    sleep( $sleepSeconds );
	    	    continue;
    		}

                if ( $callback ) {
                    call_user_func_array( $callback, array( $stats ) );
                }

                if ( $sleepSeconds ) {
                    sleep( $sleepSeconds );
                }
            }
        }

        return $stats;
    }

    /**
     * Helper, CLI output
     */
    public static function cliOutput( $msg, $addEOL = true )
    {
        global$isQuiet, $cli;

        if ( !$isQuiet && $cli ) {
            $cli->output( $msg, $addEOL );
        }
    }

    /**
     * Helper, print progres
     */
    public static function printProgress( $stats )
    {
        if ( isset($stats['updatesCount']) && (int)$stats['updatesCount'] === 1 ) {
            self::cliOutput( 'Going to purge ' . (int)$stats['totalCount'] . ' caches' );
        }

        self::cliOutput( '.', false );
    }

    /**
     * Helper, print statistics
     */
    public static function printStats( $stats )
    {
        self::cliOutput( '' );
        self::cliOutput(
            'Purged '.$stats['purgedCount']
            .' of '.$stats['totalCount'].' caches'
            .' in '.$stats['elapsedSeconds'].' seconds'
        );
        self::cliOutput( '' );
    }
}

?>
