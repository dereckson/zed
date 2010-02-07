<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Raw text or HTML content
 */

//Loads smarty language file
lang_load('motd.conf');

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