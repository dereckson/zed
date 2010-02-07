<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * SmartLine
 * 
 */

///
/// Helpers
///

/*
 * Logs a Smartline command
 * @param string $command the command to log
 * @param boolean $isError indicates if the command is an error
 */
function log_C ($command, $isError = false) {
    global $db, $CurrentPerso;
    $isError = $isError ? 1 : 0;
    $command = $db->sql_escape($command);
    $sql = "INSERT INTO "  . TABLE_LOG_SMARTLINE . " (perso_id, command_time, command_text, isError)
            VALUES ($CurrentPerso->id, UNIX_TIMESTAMP(), '$command', $isError)";
    if (!$db->sql_query($sql)) 
        message_die(SQL_ERROR, "Historique C", '', __LINE__, __FILE__, $sql);
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
     
    if ($smartLine->count(STDOUT) > 0)
        $smarty->assign("SmartLine_STDOUT", $smartLine->gets_all(STDOUT, '', '<br />'));
        
    if ($error)
        $smarty->assign("SmartLine_STDERR", $smartLine->gets_all(STDERR, '', '<br />'));
	
    if ($controller != '') {
	include($controller);
    }
    
    log_C($C, $error);
}

///
/// Gets SmartLine history
///

$sql = "SELECT command_time, command_text FROM log_smartline
        WHERE isError = 0 AND perso_id = $CurrentPerso->id
        ORDER BY command_time DESC LIMIT 100";
if (!$result = $db->sql_query($sql)) {
        message_die(SQL_ERROR, "Wiki fetching", '', __LINE__, __FILE__, $sql);
}
$i = 0;
while ($row = $db->sql_fetchrow($result)) {
    $commands[$i]->time = get_hypership_time($row['command_time']);
    $commands[$i]->text = $row['command_text'];
    $i++;
}
$smarty->assign("SmartLineHistory", $commands);
?>