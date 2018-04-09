<?php

/**
 * Application entry point
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  EntryPoints
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 * @todo        Consider to split the different tasks (especially
 *              perso select/create into several files)
 */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Keruald (formerly Pluton) library
include('includes/core.php');

//Session
$IP = encode_ip($_SERVER["REMOTE_ADDR"]);
require_once('includes/story/story.php'); //this class can be stored in session
session_start();
$_SESSION[ID] = session_id();
session_update(); //updates or creates the session

include("includes/login.php"); //login/logout
$CurrentUser = get_logged_user(); //Gets current user information

//Gets current perso
require_once('includes/objects/perso.php');
if ($perso_id = $CurrentUser->session['perso_id']) {
    $CurrentPerso = new Perso($perso_id);
}

//Skin and accent to load
define('THEME', $CurrentUser->session['Skin']);
define('ACCENT', $CurrentUser->session['Skin_accent']);

//Loads Smarty
require('includes/Smarty/Smarty.class.php');
$smarty = new Smarty();
$current_dir = dirname(__FILE__);
$smarty->setTemplateDir($current_dir . '/skins/' . THEME);

$smarty->compile_dir = CACHE_DIR . '/compiled';
$smarty->cache_dir = CACHE_DIR;
$smarty->config_dir = $current_dir;

$smarty->config_vars['StaticContentURL'] = $Config['StaticContentURL'];

//Loads language files
initialize_lang();
lang_load('core.conf');

//Gets URL
$url = get_current_url_fragments();

//If the user isn't logged in (is anonymous), prints login/invite page & dies.
if ($CurrentUser->id < 1000) {
    include('controllers/anonymous.php');
    exit;
}

////////////////////////////////////////////////////////////////////////////////
///
/// Perso (=character) selector
///

//Handles form
if ($_POST['form'] == 'perso.create') {
    $perso = null;
    $errors = array();
    if (Perso::create_perso_from_form($CurrentUser, $perso, $errors)) {
        //Notifies and logs in
        $smarty->assign('NOTIFY', lang_get('NewCharacterCreated'));
        $CurrentPerso = $perso;
        set_info('perso_id', $perso->id);
        $CurrentPerso->set_flag("site.lastlogin", $_SERVER['REQUEST_TIME']);
    } else {
        //Prints again perso create form, so the user can fix it
        $smarty->assign('WAP', join("<br />", $errors));
        $smarty->assign('perso', $perso);
    }
}

if ($_GET['action'] == 'perso.logout' && $CurrentPerso != null) {
    //User wants to change perso
    $CurrentPerso->on_logout();
    $CurrentPerso = null;
} elseif ($_GET['action'] == 'perso.select') {
    //User has selected a perso
    $CurrentPerso = new Perso($_GET['perso_id']);
    if ($CurrentPerso->user_id != $CurrentUser->id) {
        //User have made an error in the URL
        message_die(HACK_ERROR, "This isn't your perso.");
    }
    $CurrentPerso->on_select();
}

if (!$CurrentPerso) {
    switch ($count = Perso::get_persos_count($CurrentUser->id)) {
        case 0:
            //User have to create a perso
            $smarty->display("perso_create.tpl");
            exit;

        case 1:
            //Autoselects only perso
            $CurrentPerso = Perso::get_first_perso($CurrentUser->id);
            $CurrentPerso->on_select();
            break;

        default:
            //User have to pick a perso
            $persos = Perso::get_persos($CurrentUser->id);
            $smarty->assign("PERSOS", $persos);
            $smarty->display("perso_select.tpl");
            $_SESSION['UserWithSeveralPersos'] = true;
            exit;
    }
}

//Assigns current perso object as Smarty variable
$smarty->assign('CurrentPerso', $CurrentPerso);

////////////////////////////////////////////////////////////////////////////////
///
/// Tasks to execute before calling the URL controller:
///     - assert the perso is somewhere
///     - executes the smartline
///

//If the perso location is unknown, ejects it to an asteroid
if (!$CurrentPerso->location_global) {
    require_once('includes/geo/place.php');
    $smarty->assign('NOTIFY', lang_get('NewLocationNotify'));
    $CurrentPerso->move_to(GeoPlace::get_start_location());
}

//SmartLine
include("includes/SmartLine/ZedSmartLine.php");

//Redirects user to user request controller if site.requests flag on
if (defined('PersoSelected') && array_key_exists('site.requests', $CurrentPerso->flags) && $CurrentPerso->flags['site.requests']) {
    include('controllers/persorequest.php');
}

////////////////////////////////////////////////////////////////////////////////
///
/// Calls the specific controller to serve the requested page
///

switch ($controller = $url[0]) {
    case '':
        include('controllers/home.php');
        break;

    case 'builder':
    case 'explore':
    case 'page':
    case 'request':
    case 'settings':
    case 'ship':
        include("controllers/$controller.php");
        break;

    case 'who':
        include('controllers/profile.php'); //Azhàr controller
        break;

    case 'push':
        include('controllers/motd.php'); //Azhàr controller
        break;

    case 'quux':
        //It's like a test/debug console/sandbox, you put what you want into
        if (file_exists('dev/quux.php')) {
            include('dev/quux.php');
        } else {
            message_die(GENERAL_ERROR, "Quux lost in Hollywood.", "Nay");
        }
        break;

    default:
        //TODO: returns a prettier 404 page
        header("Status: 404 Not Found");
        dieprint_r($url, 'Unknown URL');
}
