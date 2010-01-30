<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Header
 */
 
//
// Graffiti wall
//
//TODO: this is a potentially very intensive SQL query
$sql = 'SELECT p.perso_nickname as username, m.motd_text FROM ' . TABLE_PERSOS . ' p, ' . TABLE_MOTD . ' m WHERE p.perso_id = m.perso_id ORDER BY rand() LIMIT 1';
if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't query MOTD", '', __LINE__, __FILE__, $sql);
$row = $db->sql_fetchrow($result);
$smarty->assign('WALL_TEXT', $row['motd_text']);
$smarty->assign('WALL_USER', $row['username']);
$smarty->assign('WALL_USER_URL', get_url('user', $row['username']));

//
// HTML output
//

//Defines DOJO if needed, and assigns DOJO/DIJIT smarty variables
if (!defined('DOJO')) define('DOJO', defined('DIJIT'));
if (defined('DIJIT')) $smarty->assign('DIJIT', true);
$smarty->assign('DOJO', DOJO);

//Prints the template
$smarty->display('header.tpl');

define('HEADER_PRINTED', true);
?>