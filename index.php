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
 */

use Zed\Engines\Perso\PersoSelector;
use Zed\Engines\Templates\Smarty\Engine as SmartyEngine;

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Keruald (formerly Pluton) library
include('includes/core.php');

//Session
$IP = $_SERVER["REMOTE_ADDR"];
require_once('includes/story/story.php'); //this class can be stored in session
session_start();
$_SESSION['ID'] = session_id();
session_update(); //updates or creates the session

include("includes/login.php"); //login/logout
$CurrentUser = get_logged_user(); //Gets current user information

//Skin and accent to load
define('THEME', $CurrentUser->session['Skin']);
define('ACCENT', $CurrentUser->session['Skin_accent']);

//Loads Smarty
$smarty = SmartyEngine::load()->getSmarty();

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

$CurrentPerso = PersoSelector::load($CurrentUser, $smarty);

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
