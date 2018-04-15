<?php

/**
 * The Zed SmartLine subcontroller.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is the SmartLine subcontroller.
 *
 * The SmartLine is a widget allowing to add some basic CLI capability.
 *
 * It executes any command given in GET or POST request (parameter C).
 *
 * This files also provides SmartLine history helper: a method log_C to log
 * a SmartLine command and some procedural code assigning a SmartLineHistory.
 *
 * This code is inspired from Viper, a corporate PHP intranet I wrote in 2004.
 * There, the SmartLine allowed to change color theme or to find quickly user,
 * account, order or server information in a CRM context.
 *
 * @package     Zed
 * @subpackage  SmartLine
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo Caches SmartLine history
 */

///
/// Helpers
///

/**
 * Logs a Smartline command
 *
 * @param string $command the command to log
 * @param bool $isError indicates if the command is an error
 */
function log_C ($command, $isError = false) {
    global $db, $CurrentPerso;
    $isError = $isError ? 1 : 0;
    $command = $db->sql_escape($command);
    $sql = "INSERT INTO "  . TABLE_LOG_SMARTLINE . " (perso_id, command_time, command_text, isError)
            VALUES ($CurrentPerso->id, UNIX_TIMESTAMP(), '$command', $isError)";
    if (!$db->sql_query($sql)) {
        message_die(SQL_ERROR, "Can't log SmartLine command", '', __LINE__, __FILE__, $sql);
    }
}

///
/// Executes command
///

if ($C = $_REQUEST['C']) {
    //Initializes SmartLine object
    require_once("SmartLine.php");
    $smartLine = new SmartLine();

    require_once("ZedCommands.php");

    //Executes SmartLine
    $controller = '';
    $smartLine->execute($C);

    $error = $smartLine->count(STDERR) > 0;

    if ($smartLine->count(STDOUT) > 0) {
        $smarty->assign("SmartLine_STDOUT", $smartLine->gets_all(STDOUT, '', '<br />'));
    }

    if ($error) {
        $smarty->assign("SmartLine_STDERR", $smartLine->gets_all(STDERR, '', '<br />'));
    }

    if ($controller != '') {
        include($controller);
    }

    log_C($C, $error);
}

///
/// Gets SmartLine history
///

$perso_id = $db->sql_escape($CurrentPerso->id);
$sql = "SELECT command_time, command_text FROM log_smartline
        WHERE isError = 0 AND perso_id = '$perso_id'
        ORDER BY command_time DESC LIMIT 100";
if (!$result = $db->sql_query($sql)) {
    message_die(SQL_ERROR, "Can't get SmartLine history", '', __LINE__, __FILE__, $sql);
}
$i = 0;
while ($row = $db->sql_fetchrow($result)) {
    $commands[$i]['time'] = get_hypership_time($row['command_time']);
    $commands[$i]['text'] = $row['command_text'];
    $i++;
}

$smarty->assign("SmartLineHistory", $commands);
