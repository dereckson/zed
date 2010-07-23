<?php

/**
 * Homepage

 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller handle the / URL.
 *
 * It prints:
 *      a scene rendering from where the perso is ;
 *      the home.tpl view ;
 *      the messages, using the messages.tpl view.
 *
 * The controller also handle messages, marking them red and allowing their
 * suppression: /?action=msg_delete&id=8 to delete the message #8.
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
 * @todo The homepage requires Dojo but Dojo loading here is currently a kludge, as dojo is required by hypership .tpl scene. We should create an optionnal .meta xml file format to set this kind of options.
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
if (!defined('DIJIT')) {
    /**
     * This constant indicates we need to load the Dijit (and so Dojo) library.
     */
    define('DIJIT', true);
}

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