<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Cron
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