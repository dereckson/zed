<?php

/**
 * AJAX callbacks
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * As main controller could potentially be interrupted (e.g. if site.requests
 * flag is at 1, user is redirected to controllers/userrequest.php), all AJAX
 * queries should be handled by this script and not directly by the controllers.
 *
 * Standard return values:
 *  -7  user is logged but perso isn't selected,
 *  -9  user is not logged.
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

use Zed\Engines\Database\Database;
use Zed\Engines\Templates\Smarty\Engine as SmartyEngine;
use Hypership\Geo\Point3D;

////////////////////////////////////////////////////////////////////////////////
///
/// Constants
///

//We define one negative number constant by standard erroneous return value.

/**
 * Magic number which indicates the user is not logged in.
 */
define('USER_NOT_LOGGED', -9);

/**
 * Magic number which indicates the user is logged in, but haven't selected its perso.
 */
define('PERSO_NOT_SELECTED', -7);

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

include('includes/core.php');

//Database
$db = Database::load($Config['Database']);

//Session
$IP = $_SERVER["REMOTE_ADDR"];
require_once('includes/story/story.php'); //this class can be stored in session
session_start();
$_SESSION['ID'] = session_id();
session_update(); //updates or creates the session

//Gets current perso
require_once('includes/objects/perso.php');
$CurrentUser = get_logged_user(); //Gets current user infos
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
$smarty = SmartyEngine::load()->getSmarty();

//Loads language files
initialize_lang();
lang_load('core.conf');

////////////////////////////////////////////////////////////////////////////////
///
/// Actions definitions
///

/**
 * Actions class
 *
 * Each method is called by first part of your URL, other parts are arguments
 * e.g. /do.php/validate_quux_request/52 = Actions::validate_quux_request(52);
 *
 * You can also use $_GET, $_POST or better $_REQUEST.
 *
 * Don't print the value but return it, so we can in the future implement custom
 * formats like api_output();
 */
class Actions {
    /**
     * Checks the arguments hash and determines whether it is valid.
     *
     * @param Array $args the arguments, the last being the hash
     * @return boolean true if the hash is valid ; otherwise, false.
     */
    static private function is_hash_valid ($args) {
        global $Config;
        return array_pop($args) == md5($_SESSION['ID'] . $Config['SecretKey'] . implode('', $args));
    }

    /**
     * Handles a allow/deny perso request.
     *
     * @param string $request_flag the request flag to clear
     * @param string $store 'perso' or 'registry'
     * @param string $key the perso flag or registry key
     * @param string $value the value to store
     * @param string $hash the security hash
     * @return boolean true if the request is valid and have been processed ; otherwise, false.
     */
    static function perso_request ($request_flag, $store, $key, $value, $hash) {
        global $CurrentPerso;

        //Ensures we've the correct amount of arguments
        if (func_num_args() < 4) {
            return false;
        }

        //Checks hash
        $args = func_get_args();
        if (!self::is_hash_valid($args)) {
            return false;
        }

        //Sets flag
        switch ($store) {
            case 'perso':
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
        if ((string)$request_flag !== "0") {
            $CurrentPerso->delete_flag($request_flag);
        }

        return true;
    }

    /**
     * Sets current perso's local location.
     *
     * We don't require a security hash. If the users want to play with it, no problem.
     * You generally move inside a global location as you wish.
     * So, if you write a story capturing a perso, use flags to handle this escape!
     *
     * @param string $location_local the local location
     * @return GeoLocation the current perso's GeoLocation object
     */
    static function set_local_location ($location_local) {
        global $CurrentPerso;

        //Ensures we've the correct amount of arguments
        if (func_num_args() < 1) {
            return null;
        }

        //Moves current perso to specified location
        $location_local = urldecode($location_local);
        $CurrentPerso->move_to(null, $location_local);

        //Returns GeoLocation relevant instance
        return $CurrentPerso->location;
    }

    /**
     * Moves the current perso's, setting a new local location.
     *
     * We don't require a security hash. If the users want to play with it, no problem.
     * You generally move inside a global location as you wish.
     * So, if you write a story capturing a perso, use flags to handle this escape!
     *
     * @param string $move the move (coordinates or direction)
     * @param int $factor a number multiplying the specified move [optional]
     * @return GeoLocation the current perso's GeoLocation object
     *
     * e.g. to move from 2 units to east, you can use one of those instructions:
     *  local_move('east', 2);
     *  local_move('2,0,0');
     *  local_move('1,0,0', 2);
     *
     * Valid moves string are north, east, south, west, up and down.
     * Valid moves coordinates are x,y,z (3 integers, comma as separator)
     */
    static function local_move ($move, $factor = 1) {
        global $CurrentPerso;

        //Ensures we've the correct amount of arguments
        if (func_num_args() < 1) {
            return null;
        }

        //Parses $move
        switch ($move) {
            case 'north':
                $move = [0, 1, 0];
                break;

            case 'east':
                $move = [1, 0, 0];
                break;

            case 'south':
                $move = [0, -1, 0];
                break;

            case 'west':
                $move = [-1, 0, 0];
                break;

            case 'up':
                $move = [0, 0, 1];
                break;

            case 'down':
                $move = [0, 0, -1];
                break;

            default:
                $move = explode(',', $move, 3);
                foreach ($move as $coordinate) {
                    if (!is_numeric($coordinate)) {
                        return null;
                    }
                }
        }

        //Moves current perso to specified location
        if ($location_local = Point3D::fromString($CurrentPerso->location->local)) {
            $location_local->translate(
                (float)$move[0] * $factor,
                (float)$move[1] * $factor,
                (float)$move[2] * $factor
            );
            $CurrentPerso->move_to(null, $location_local->sprintf("(%d, %d, %d)"));

            //Returns GeoLocation relevant instance
            return $CurrentPerso->location;
        }

        //Old local location weren't a Point3D
        return null;
    }

    /**
     * Moves the current perso's, setting a new local location, using polar+z coordinates.
     * Polar+z coordinates are polar coordinates, plus a cartesian z dimension.
     *
     * We don't require a security hash. If the users want to play with it, no problem.
     * You generally move inside a global location as you wish.
     * So, if you write a story capturing a perso, use flags to handle this escape!
     *
     * @param string $move the move (coordinates or direction)
     * @param int $factor a number multiplying the specified move [optional]
     * @return GeoLocation the current perso's GeoLocation object
     *
     * Valid moves string are cw, ccw, out, in, up and down.
     *  r: out = +12   in  = -12
     *  °: cw  = +20°  ccw = -20
     * Valid moves coordinates are r,°,z (3 integers, comma as separator)
     *                                   (the medium value can also be integer + °)
     *
     * e.g. to move of two units (the unit is 20°) clockwise:
     *  polarz_local_move('cw', 2);
     *  polarz_local_move('(0, 20°, 0)', 2);
     *  polarz_local_move('(0, 40°, 0)');
     * Or if you really want to use radians (PI/9 won't be parsed):
     *  polarz_local_move('(0, 0.6981317007977318, 0)';
     *
     */
    static function polarz_local_move ($move, $factor = 1) {
        global $CurrentPerso;

        //Ensures we've the correct amount of arguments
        if (func_num_args() < 1) {
            return null;
        }

        //Parses $move
        $move = urldecode($move);
        switch ($move) {
            case 'cw':
                $move = [0, '20°', 0];
                break;

            case 'ccw':
                $move = [0, '-20°', 0];
                break;

            case 'in':
                $move = [+12, 0, 0];
                break;

            case 'out':
                $move = [-12, 0, 0];
                break;

            case 'up':
                $move = [0, 0, 1];
                break;

            case 'down':
                $move = [0, 0, -1];
                break;

            default:
                $move = explode(',', $move, 3);
                foreach ($move as $coordinate) {
                    if (!is_numeric($coordinate) && !preg_match("/^[0-9]+ *°$/", $coordinate)) {
                        return null;
                    }
                }
        }
        dieprint_r($move);

        //Moves current perso to specified location
        throw new Exception("Move is not implemented.");

        //Old local location weren't a 3D point
        return null;
    }

    /**
     * Moves the current perso's, setting a new global and local location.
     *
     * @param string $location_global The global location
     * @param string $location_local The local location
     * @return GeoLocation the current perso's GeoLocation object
     */
    static function global_move ($location_global, $location_local = null) {
        //Ensures we've the correct amount of arguments
        if (func_num_args() < 1) {
            return null;
        }

        //Checks hash
        $args = func_get_args();
        if (!self::is_hash_valid($args)) {
            return false;
        }

        //Moves
        global $CurrentPerso;
        $CurrentPerso->move_to($location_global, $location_local);
        return $CurrentPerso->location;
    }

    /**
     * Handles upload content form.
     *
     * @return string new content path
     */
    static function upload_content () {
        global $CurrentPerso, $CurrentUser;
        require_once('includes/objects/content.php');

        //Initializes a new content instance
        $content = new Content();
        $content->load_from_form();
        $content->user_id = $CurrentUser->id;
        $content->perso_id = $CurrentPerso->id;
        $content->location_global = $CurrentPerso->location_global;

        //Saves file
        if ($content->handle_uploaded_file($_FILES['artwork'])) {
            $content->save_to_database();
            $content->generate_thumbnail();
            return true;
        }

        return false;
    }

    /**
     * Gets multimedia content for the specified location
     *
     * @param string $location_global The global location (local is to specified in ?location_local parameter)
     * @return Array an array of Content instances
     */
    static function get_content ($location_global) {
        //Ensures we've the correct amount of arguments
        if (func_num_args() < 1) {
            return null;
        }

        //Checks hash
        $args = func_get_args();
        if (!self::is_hash_valid($args)) {
            return false;
        }

        //Checks local location is specified somewhere (usually in $_GET)
        if (!array_key_exists('location_local', $_REQUEST)) {
            return false;
        }

        //Gets content
        require_once('includes/objects/content.php');
        return Content::get_local_content($location_global, $_REQUEST['location_local']);
    }
}

////////////////////////////////////////////////////////////////////////////////
///
/// Handles request
///

//Parses URL
$Config['SiteURL'] = get_server_url() . $_SERVER["PHP_SELF"];
$args = get_current_url_fragments();

$method = array_shift($args);

if ($_REQUEST['debug']) {
    //Debug version
    //Most of E_STRICT errors are evaluated at the compile time thus such errors
    //are not reported
    ini_set('display_errors', 'stderr');
    error_reporting(-1);
    if (method_exists('Actions', $method)) {
        $result = call_user_func_array(['Actions', $method], $args);
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
        $result = @call_user_func_array(['Actions', $method], $args);

        if (array_key_exists('redirectTo', $_REQUEST)) {
            //If user JS disabled, you can add ?redirectTo= followed by an URL
            header("location: " . $_REQUEST['redirectTo']);
        } else {
            echo json_encode($result);
        }
    }
}
