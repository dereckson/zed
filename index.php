<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Application entry point
 */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Pluton library
include('includes/core.php');

//Session
$IP = encode_ip($_SERVER["REMOTE_ADDR"]);
require_once('includes/story/story.php'); //this class can be stored in session
session_start();
$_SESSION[ID] = session_id();
session_update(); //updates or creates the session

include("includes/login.php"); //login/logout
$CurrentUser = get_logged_user(); //Gets current user infos

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
$smarty->template_dir = $current_dir . '/skins/' . THEME;

$smarty->compile_dir = $current_dir . '/cache/compiled';
$smarty->cache_dir = $current_dir . '/cache';
$smarty->config_dir = $current_dir;

$smarty->config_vars['StaticContentURL'] = $Config['StaticContentURL'];

//Loads language files
initialize_lang();
lang_load('core.conf');

if ($CurrentUser->id < 1000) {   
    //Anonymous user, proceed to login
    if (array_key_exists('LastUsername', $_COOKIE))
        $smarty->assign('username', $_COOKIE['LastUsername']);
    if (array_key_exists('LastOpenID', $_COOKIE))
        $smarty->assign('OpenID', $_COOKIE['LastOpenID']);
    $smarty->assign('LoginError', $LoginError);
    $smarty->display('login.tpl');
    exit;
}

////////////////////////////////////////////////////////////////////////////////
///
/// Perso selector
///

//Handles form
if ($_POST['form'] == 'perso.create') {
    $perso = new Perso();
    $perso->load_from_form();
    $perso->user_id = $CurrentUser->id;
    
    //Validates forms
    if (!$perso->name) $errors[] = lang_get("NoFullnameSpecified");
    if (!$perso->race) {
        $errors[] = lang_get("NoRaceSpecified");
        $perso->race = "being";
    }
    if (!$perso->sex) $errors[] = lang_get("NoSexSpecified");
    if (!$perso->nickname) {
        $errors[] = lang_get("NoNicknameSpecified");
    } else if (!Perso::is_available_nickname($perso->nickname)) {
        $errors[] = lang_get("UnavailableNickname");
    }
    
    //Save or prints again forms
    if (!$errors) {
        $perso->save_to_database();
        $smarty->assign('NOTIFY', lang_get('NewCharacterCreated'));
        $CurrentPerso = $perso;
        set_info('perso_id', $perso->id);
        $CurrentPerso->set_flag("site.lastlogin", $_SERVER['REQUEST_TIME']);
    } else {
        $smarty->assign('WAP', join("<br />", $errors));
        $smarty->assign('perso', $perso);
    }    
}

if ($_GET['action'] == 'perso.logout') {
    //User wants to change perso
    $CurrentPerso->on_logout();
    $CurrentPerso = null;
} elseif ($_GET['action'] == 'perso.select') {
    //User have selected a perso
    $CurrentPerso = new Perso($_GET['perso_id']);
    if ($CurrentPerso->user_id != $CurrentUser->id) {
        //Hack
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

$url = get_current_url_fragments();
    
switch ($controller = $url[0]) {
    case '':
        include('controllers/home.php');
        break;

    case 'request':
    case 'page':
    case 'explore':
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
        //TODO: returns a 404 error
        dieprint_r($url, 'Unknown URL');
}

?>