<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Homepage
 */

//
// Gets and manage messages
//

require_once('includes/objects/message.php');

//Deletes a message if user have clicked the X
if ($_GET['action'] == 'msg_delete') {
    //Deletes message $_GET['id']
    $id = $_GET['id'];
    $messageToDelete = new Message($id);
    if ($messageToDelete->to != $CurrentPerso->id) {
        //Not one of user message
        $smarty->assign('WAP', lang_get('NotYourMessage'));
    } elseif ($messageToDelete->flag == 2) {
        //Already deleted
        $smarty->assign('WAP', lang_get('MessageAlreadyDeleted'));
    } else {
        $messageToDelete->delete();
        $smarty->assign('NOTIFY', lang_get('MessageDeleted'));
    }
}

//Gets messages
$newMessagesCount = 0;
$messages = Message::get_messages($CurrentPerso->id, true, $newMessagesCount);
if ($newMessagesCount > 0) {
    $smarty->assign('NOTIFY', sprintf(lang_get("NewMessages"), $newMessagesCount, s($newMessagesCount)));
}

//Gets scene
require_once("includes/geo/scene.php");
$scene = new GeoScene($CurrentPerso->location);
$smarty->assign('SCENE', $scene);

//
// HTML output
//

//Serves header

//TODO: Dojo loading here is currently a kludge, as dojo is required by
//hypership .tpl scene. We should create an optionnal .meta xml file format
//to set this kind of options
if (!defined('DIJIT')) define('DIJIT', true);

$smarty->assign('PAGE_TITLE', lang_get('Welcome'));
include('header.php'); 

//Serves content
if (!$scene->lastError)
    $scene->render();
    
$smarty->display('home.tpl');

if ($messages) {
    $smarty->assign('MESSAGES', $messages);
    $smarty->display('messages.tpl');
}

//Serves footer
$smarty->assign("screen", "Home console");
include('footer.php');
 
?>