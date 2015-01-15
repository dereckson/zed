<?php

/**
 * Cron
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file contains tasks to execute periodically.
 * When editing this file, ensure it works from the command line, so it's
 * possible to run it from a crontab calling PHP CLI.
 *
 * @package     Zed
 * @subpackage  Utilities
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 * @todo        Adds some periodicity (e.g. hourly, daily, monthly)
 */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Pluton library
include('includes/core.php');

//Debug mode?
$debug = false;

////////////////////////////////////////////////////////////////////////////////
///
/// Daily tasks
///

//Orders perso table by nickname.
//Rationale: prints an ordered perso select list, help for new persos, printed at end
$queries[] = "ALTER TABLE " . TABLE_PERSOS . " ORDER BY perso_nickname";

////////////////////////////////////////////////////////////////////////////////
///
/// Executes tasks
///

foreach ($queries as $query) {
    if (!$db->sql_query($sql) && $debug)
        message_die(SQL_ERROR, "Can't execute query", '', __LINE__, __FILE__, $sql);
}

?>