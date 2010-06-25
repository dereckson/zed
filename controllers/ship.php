<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Raw text or HTML content
 */

//
// Ship information
//

//Gets ship from URL
if  (count($url) < 2) {
    //No parameter, gets ship perso is onboard
    if (!$code = $CurrentPerso->location->ship_code) {
        message_die(GENERAL_ERROR, "/ship/ must be followed by valid ship code.<br />/ship alone only works when you're aboard a ship", "URL error");
    }
    $code = 'S' . $code;
} else {
    //Code have been specified
    $code = $url[1];
    if (!ereg("^S[0-9]{5}$", $code)) {
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
 
?>