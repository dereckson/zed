<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * API entry point
 * 
 */

//API Preferences
define('URL', 'http://' . $_SERVER['HTTP_HOST'] . '/index.php');

//Pluton library
require_once('includes/core.php');
require_once('includes/config.php');

//API libs
require_once('includes/api/api_helpers.php');
require_once('includes/api/cerbere.php');

//Use our URL controller method if you want to mod_rewrite the API
$url = explode('/', substr($_SERVER['PATH_INFO'], 1));

switch ($module = $url[0]) {
    case '':
        //Nothing to do
        //TODO: offer documentation instead
        die();
    
    case 'time':
        //Hypership time
        api_output(get_hypership_time(), "time");
        break;
    
    case 'location':
        //Checks creditentials
        cerbere();
        //Gets location info
        require_once("includes/geo/location.php");
        $location = new GeoLocation($url[1], $url[2]);
        api_output($location, "location");
        break;
    
    //case 'perso':
    //    //Checks creditentials
    //    cerbere();
    //    //Gets perso info
    //    require_once("includes/objects/perso.php");
    //    $perso = new Perso($url[1]);
    //    api_output($perso, "perso");
    //    break;
    
    case 'ship':
        //Ship API
        
        //Gets ship from Ship API key (distinct of regular API keys)
        require_once('includes/objects/ship.php');
        $ship = Ship::from_api_key($_REQUEST['key']) or cerbere_die("Invalid ship API key");
        
        switch ($command = $url[1]) {
            case '':
                //Nothing to do
                //TODO: offer documentation instead
                die();
                
            case 'authenticate':
                //TODO: web authenticate
                break;
            
            case 'appauthenticate':
                //Allows desktop application to authenticate
                $tmp_session_id = $url[2] or cerbere_die("/appauthenticate/ must be followed by any session identifier");
                if ($_REQUEST['name']) {
                    //Perso will be offered auth invite at next login.
                    //Handy for devices like PDA, where it's not easy to auth.
                    $perso = new Perso($_REQUEST['name']);
                    if ($perso->lastError) {
                        cerbere_die($perso->lastError);
                    }
                    if (!$ship->is_perso_authenticated($perso->id)) {
                        $ship->request_perso_authenticate($perso->id);
                    }
                    $ship->request_perso_confirm_session($tmp_session_id, $perso->id);
                } else {
                    //Delivers an URL. App have to redirects user to this URL
                    //launching a browser or printing the link.
                    $ship_code = $ship->get_code();
                    registry_set("api.ship.session.$ship_code.$tmp_session_id", -1);
                    $url = get_server_url() . get_url() . "?action=api.ship.appauthenticate&session_id=" . $tmp_session_id;
                    api_output($url, "URL");
                }
                break;
            
            case 'appauthenticated':
                //Checks the authentication
                $tmp_session_id = $url[2] or cerbere_die("/appauthenticated/ must be followed by any session identifier you used in /appauthenticate");
                $perso_id = $ship->get_perso_from_session($tmp_session_id);
                if (!$isPersoAuth = $ship->is_perso_authenticated($perso_id)) {
                    //Global auth not ok/revoked.
                    $auth->status = -1;
                } else {
                    $perso = Perso::get($perso_id);
                    $auth->status = 1;
                    $auth->perso->id = $perso->id;
                    $auth->perso->nickname = $perso->nickname;
                    $auth->perso->name = $perso->name;
                    //$auth->perso->location = $perso->location;
                    //Is the perso on board? Yes if its global location is S...
                    $auth->perso->onBoard = (
                        $perso->location_global[0] == 'S' &&
                        substr($perso->location_global, 1, 5) == $ship->id
                    );
                    if ($auth->perso->onBoard) {
                        //If so, give local location
                        $auth->perso->location_local = $perso->location_local;
                    }
                }
                api_output($auth, "auth");
                break;
        }
        break;
    
    default:
        echo "Unknown module:";
        dprint_r($url);
        break;
}

?>