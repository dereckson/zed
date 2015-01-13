<?php

/**
 * Header
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller handle the header (MOTD, html header)
 *
 * @package     Zed
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo cache MOTD fragments (sql performance)
 */
 
//
// MOTD
//

//TODO: this is a potentially very intensive SQL query
$sql = 'SELECT p.perso_nickname as username, p.perso_name as name, m.motd_text FROM ' . TABLE_PERSOS . ' p, ' . TABLE_MOTD . ' m WHERE p.perso_id = m.perso_id ORDER BY rand() LIMIT 1';
if (!$result = $db->sql_query($sql)) message_die(SQL_ERROR, "Can't query MOTD", '', __LINE__, __FILE__, $sql);
$row = $db->sql_fetchrow($result);
$smarty->assign('WALL_TEXT', $row['motd_text']);
$smarty->assign('WALL_USER', $row['name']);
$smarty->assign('WALL_USER_URL', get_url('who', $row['username']));

//
// HTML output
//

//Defines DOJO if needed, and assigns DOJO/DIJIT smarty variables
if (!defined('DOJO')) {
    /**
     * Determines if the dojo library have or not been loaded
     *
     * If true, there's a code like <script src="js/dojo/dojo/dojo.js"><script>
     * in the <head> block of the code.
     */
    define('DOJO', defined('DIJIT'));
}

if (defined('DIJIT')) $smarty->assign('DIJIT', true);
$smarty->assign('DOJO', DOJO);

//Prints the template
$smarty->display('header.tpl');

/**
 * This constant indicates the header have been printed
 */
define('HEADER_PRINTED', true);
?>
