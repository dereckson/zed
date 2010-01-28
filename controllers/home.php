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
    if ($messageToDelete->to != $CurrentUser->id) {
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
$messages = Message::get_messages($CurrentUser->id);
$smarty->assign('MESSAGES', $messages);

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', lang_get('Welcome'));
include('header.php'); 

//Serves content
$smarty->display('home.tpl');
if ($messages)
    $smarty->display('messages.tpl');

//Serves footer
$smarty->assign("screen", "Home console");
include('footer.php');
 
?>