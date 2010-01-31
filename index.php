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

//Loads language files
define('LANG', 'fr');
lang_load('core.conf');

if ($CurrentUser->id < 1000) {
    //Anonymous user, proceed to login
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
        $CurrentPerso->setflag("site.lastlogin", $_SERVER['REQUEST_TIME']);
    } else {
        $smarty->assign('WAP', join("<br />", $errors));
        $smarty->assign('perso', $perso);
    }    
}

if ($_GET['action'] == 'perso.logout') {
    //User wants to change perso
    $CurrentPerso = null;
    set_info('perso_id', null);
} elseif ($_GET['action'] == 'perso.select') {
    //User have selected a perso
    $CurrentPerso = new Perso($_GET['perso_id']);
    if ($CurrentPerso->user_id != $CurrentUser->id) {
        //Hack
        message_die(HACK_ERROR, "This isn't your perso.");
    }
    set_info('perso_id', $CurrentPerso->id);
    $CurrentPerso->setflag("site.lastlogin", $_SERVER['REQUEST_TIME']);
}

if (!$CurrentPerso) {   
    switch ($count = Perso::get_persos_count($CurrentUser->id)) {
        case 0:
            //Create a perso
            $smarty->display("perso_create.tpl");
            exit;
        
        case 1:
            //Autoselect
            $CurrentPerso = Perso::get_first_perso($CurrentUser->id);
            set_info('perso_id', $CurrentPerso->id);
            $CurrentPerso->setflag("site.lastlogin", $_SERVER['REQUEST_TIME']);
            break;
            
        default:
            //Pick a perso
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

////////////////////////////////////////////////////////////////////////////////
///
/// Calls the specific controller to serve the requested page
///

$url = explode('/', substr($_SERVER['PATH_INFO'], 1));

switch ($controller = $url[0]) {
    case '':
        include('controllers/home.php');
        break;

    case 'request':
        include("controllers/$controller.php");
        break;


    default:
        //TODO: returns a 404 error
        dieprint_r($url, 'Unknown URL');
}

?>