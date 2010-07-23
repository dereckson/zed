<?php

/**
 * MOTD

 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller handle the /push "secret" URL.
 *
 * It allows to add a message in the MOTD (messages printed in the header on
 * the top of each page).
 *
 * It usees the motd_add.tpl view and the MOTD class.
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
 */

//
// Handles form
//

if ($_REQUEST['text']) {
    require_once('includes/objects/motd.php');
    $motd = new MOTD();
    $motd->text = $_REQUEST['text'];
    $motd->perso_id = $CurrentPerso->id;
    $motd->save_to_database();
    $smarty->assign('WAP', lang_get('Published'));
}

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', lang_get('PushMessage'));
include('header.php'); 

//Serves content
$smarty->display('motd_add.tpl');

//Servers footer
include('footer.php');
 
?>