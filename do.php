<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * AJAX callbacks
 *
 * As main controller could potentially be interrupted (e.g. if site.requests
 * flag is at 1, user is redirected to controllers/userrequest.php), all AJAX
 * queries should be handled by this script and not directly by the controllers.
 *
 * Standard return values:
 *  -7  user is logged but perso isn't selected
 *  -9  user is not logged
 *
 */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Standard return values
define('USER_NOT_LOGGED', -9);
define('PERSO_NOT_SELECTED', -7);

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

//Requires user and perso
if ($CurrentUser->id < 1000) {
    echo USER_NOT_LOGGED;
    exit;
}
if (!$CurrentPerso) {
    echo PERSO_NOT_SELECTED; 
    exit;
}

//Loads Smarty (as it handles l10n, it will be used by lang_get)
require('includes/Smarty/Smarty.class.php');
$smarty = new Smarty();
$current_dir = dirname(__FILE__);
$smarty->compile_dir = $current_dir . '/cache/compiled';
$smarty->cache_dir = $current_dir . '/cache';
$smarty->config_dir = $current_dir;

//Loads language files
initialize_lang();
lang_load('core.conf');


////////////////////////////////////////////////////////////////////////////////
///
/// Actions definitions
///

/*
 * Actions class
 * Each method is called by first part of your URL, other parts are arguments
 * e.g. /do.php/validate_quux_request/52 = Actions::validate_quux_request(52);
 *
 * You can also use $_GET, $_POST or better $_REQUEST.
 *
 * Don't echo the value but return it, so we can in the future implement custom
 * formats like api_output();
 */

class Actions {
    /*
     * Checks the arguments hash
     * @param Array $args the arguments, the last being the hash
     */
    static private function is_hash_valid ($args) {
        global $Config;
        return array_pop($args) == md5($_SESSION['ID'] . $Config['SecretKey'] . implode('', $args));
    }
    
    /*
     * Handles a allow/deny perso request
     * @param string $request_flag the request flag to clear
     * @param string $store 'perso' or 'registry'
     * @param string $key the perso flag or registry key
     * @param string $value the value to store
     * @param string $hash the security hash
     * @return boolean true if the request is valid and have been processed ; otherwise, false.
     */
    static function perso_request ($request_flag, $store, $key, $value, $hash) {
        //Ensures we've the correct amount of arguments
        if (func_num_args() < 4) return false;
        
        //Checks hash
        $args = func_get_args();
        if (!self::is_hash_valid($args)) {
            return false;
        }
                
        //Sets flag
        switch ($store) {
            case 'perso':
                global $CurrentPerso;
                $CurrentPerso->set_flag($key, $value);
                break;
            
            case 'registry':
                registry_set($key, $value);
                break;
            
            default:
                //Unknown storage location
                return false;
        }
        
        //Clears request flag
        if ($request_flag != 0)
            $CurrentPerso->delete_flag($request_flag);
        
        return true;
    }
}

////////////////////////////////////////////////////////////////////////////////
///
/// Handles request
///

//You really should use $_SERVER['PATH_INFO']
//i.e. calling /do.php/your request without any mod rewrite intervention
//
//If you choose otherwise, uncomment and tweak one of the following lines:
//$Config['SiteURL'] = 'http://yourserver/zed/do.php';
//$Config['SiteURL'] = get_server_url() . '/do.php';
$args = get_current_url_fragments();

$method = array_shift($args);

if ($_REQUEST['debug']) {
    //Debug version
    //Most of E_STRICT errors are evaluated at the compile time thus such errors
    //are not reported
    ini_set('display_errors', 'stderr');
    error_reporting(-1);
    if (method_exists('Actions', $method)) {
        $result = call_user_func_array(array('Actions', $method), $args);
        echo json_encode($result);
    } else {
        echo "<p>Method doesn't exist: $method</p>";
    }
    
    if (array_key_exists('redirectTo', $_REQUEST)) {
        //If user JS disabled, you can add ?redirectTo= followed by an URL
        echo "<p>Instead to print a callback value, redirects to <a href=\"$_REQUEST[redirectTo]\">$_REQUEST[redirectTo]</a></p>";
    }    
} else {
    //Prod version doesn't prints warning <== silence operator
    if (method_exists('Actions', $method)) {
        $result = @call_user_func_array(array('Actions', $method), $args);
        
        if (array_key_exists('redirectTo', $_REQUEST)) {
            //If user JS disabled, you can add ?redirectTo= followed by an URL
            header("location: " . $_REQUEST['redirectTo']);
        } else {        
            echo json_encode($result);
        }
    }
}

?>