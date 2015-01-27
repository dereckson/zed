<?php

/**
 * Ship
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller handle the /ship URL
 *
 * It allows the user to let personal notes about the ship.
 *
 * It uses the Ship model and the ship.tpl view
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
 * @todo prints information indicating if we're or not in the ship
 * @todo implement a console to control the ship
 */

//
// Load library and language file
//

require_once('includes/objects/ship.php');
lang_load('ships.conf');

//
// Ship information
//

//Gets ship from URL
if (count($url) < 2) {
    //No parameter, gets ship perso is onboard
    if (!$code = $CurrentPerso->location->ship_code) {
        message_die(GENERAL_ERROR, "/ship/ must be followed by valid ship code.<br />/ship alone only works when you're aboard a ship", "URL error");
    }
    $code = 'S' . $code;
} else {
    //Code have been specified
    $code = $url[1];
    if (!preg_match("/^S[0-9]{5}$/", $code)) {
        message_die(GENERAL_ERROR, "/ship/ must be followed by valid ship code", "URL error");
    }
}

//Gets ship information
$ship = Ship::get($code);

//Gets perso note about this ship
$note = $CurrentPerso->get_note($code);

//Determines the spatial relation between perso and ship
//dieprint_r($CurrentPerso->location->ship_code);

//
// Actions handling
//
if ($_REQUEST['action'] == 'ship.setnote' && $_REQUEST['note'] != $note) {
    //Updates note content
    $CurrentPerso->set_note($code, $_REQUEST['note']);
    $note = $_REQUEST['note'];
}

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', $ship->name);
include('header.php');

//Serves content
$smarty->assign('note', $note);
$smarty->assign('ship', $ship);
$smarty->display('ship.tpl');

//Serves footer
include('footer.php');
