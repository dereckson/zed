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
    
    case 'perso':
        //Checks creditentials
        cerbere();
        //Gets perso info
        require_once("includes/objects/perso.php");
        $perso = new Perso($url[1]);
        api_output($perso, "perso");
        break;
    
    default:
        echo "Unknown module:";
        dprint_r($url);
        break;
}

?>