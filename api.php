<?php

/**
 * API entry point
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
 * @todo        Consider to output documentation on / and /ship queries
 * @todo        /app/getdata
 */

use Zed\Engines\API\Response;
use Zed\Engines\Database\Database;
use Zed\Models\Geo\Galaxy;
use Zed\Models\Geo\Location;
use Zed\Models\Objects\Application;
use Zed\Models\Objects\Perso;
use Zed\Models\Objects\Ship;

//API preferences
define('URL', 'http://' . $_SERVER['HTTP_HOST'] . '/index.php');

//Pluton library
require_once('includes/core.php');
require_once('includes/config.php');

//Use our URL controller method if you want to mod_rewrite the API
$Config['SiteURL'] = get_server_url() . $_SERVER["PHP_SELF"];
$url = get_current_url_fragments();

//Database
$db = Database::load($Config['database']);

//API response
$format = $_REQUEST['format'] ?? 'preview';
$apiResponse = Response::withFormat($db, $format);

switch ($module = $url[0]) {
/*  -------------------------------------------------------------
    Site API

    /time
    /location
    /coordinates
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    case '':
        //Nothing to do
        //TODO: offer documentation instead
        die();

    case 'time':
        //Hypership time
        $apiResponse->output(get_hypership_time(), "time");
        break;

    case 'location':
        //Checks credentials
        $apiResponse->guard();
        //Gets location info
        $location = new Location($db, $url[1], $url[2]);
        $apiResponse->output($location, "location");
        break;

    case 'coordinates':
        //Checks credentials
        $apiResponse->guard();
        //Get coordinates
        $apiResponse->output(Galaxy::getCoordinates($db), 'galaxy', 'object');
        break;

/*  -------------------------------------------------------------
    Ship API

    /authenticate
    /appauthenticate
    /appauthenticated
    /move
    /land
    /flyout
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    case 'ship':
        //Ship API

        //Gets ship from Ship API key (distinct of regular API keys)
        $ship = Ship::from_api_key($_REQUEST['key']) or $apiResponse->die("Invalid ship API key");

        switch ($command = $url[1]) {
            case '':
                //Nothing to do
                //TODO: offer documentation instead
                die();

            case 'authenticate':
                //TODO: web authenticate
                break;

            case 'appauthenticate':
                //Allows desktop application to authenticate an user
                $tmp_session_id = $url[2] or $apiResponse->die("/appauthenticate/ must be followed by any session identifier");
                if ($_REQUEST['name']) {
                    //Perso will be offered auth invite at next login.
                    //Handy for devices like PDA, where it's not easy to auth.
                    $perso = new Perso($db, $_REQUEST['name']);
                    if ($perso->lastError) {
                        $apiResponse->die($perso->lastError);
                    }
                    if (!$ship->is_perso_authenticated($perso->id)) {
                        $ship->request_perso_authenticate($perso->id);
                    }
                    $ship->request_perso_confirm_session($tmp_session_id, $perso->id);
                } else {
                    //Delivers an URL. App have to redirects user to this URL
                    //launching a browser or printing the link.
                    $ship_code = $ship->get_code();
                    registry_set($db, "api.ship.session.$ship_code.$tmp_session_id", -1);
                    $url = get_server_url() . get_url() . "?action=api.ship.appauthenticate&session_id=" . $tmp_session_id;
                    $apiResponse->output($url, "URL");
                }
                break;

            case 'appauthenticated':
                //Checks the user authentication
                $tmp_session_id = $url[2] or $apiResponse->die("/appauthenticated/ must be followed by any session identifier you used in /appauthenticate");
                $perso_id = $ship->get_perso_from_session($tmp_session_id);
                if (!$isPersoAuth = $ship->is_perso_authenticated($perso_id)) {
                    //Global auth not ok/revoked.
                    $auth->status = -1;
                } else {
                    $perso = Perso::get($db, $perso_id);
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
                $apiResponse->output($auth, "auth");
                break;

            case 'move':
                //Moves the ship to a new location, given absolute coordinates
                //TODO: handle relative moves
                if (count($url) < 2) {
                    $apiResponse->die("/move/ must be followed by a location expression");
                }

                //Gets location class
                //It's allow: (1) to normalize locations between formats
                //            (2) to ensure the syntax
                //==> if the ship want to communicate free forms coordinates, must be added on Location a free format
                try {
                    $location = new Location($db, $url[2]);
                } catch (Exception $ex) {
                    $reply->success = 0;
                    $reply->error = $ex->getMessage();
                    $apiResponse->output($reply, "move");
                    break;
                }

                $ship->location_global = $location->global;
                $ship->save_to_database();

                $reply->success = 1;
                $reply->location = $ship->location;
                $apiResponse->output($reply, "move");
                break;

            case 'land':
            case 'flyin':
                //Flies in
                try {
                    $location = new Location($db, $location);
                } catch (Exception $ex) {
                    $reply->success = 0;
                    $reply->error = $ex->getMessage();
                    $apiResponse->output($reply, "land");
                    break;
                }

                break;

            case 'flyout':
                //Flies out

                break;

        }
        break;

/*  -------------------------------------------------------------
    Application API

    /checkuserkey
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

    case 'app':
        //Application API
        $app = Application::from_api_key($db, $_REQUEST['key']) or $apiResponse->die("Invalid application API key");

        switch ($command = $url[1]) {
            case '':
                //Nothing to do
                //TODO: offer documentation instead
                die();

            case 'checkuserkey':
                if (count($url) < 2) {
                    $apiResponse->die("/checkuserkey/ must be followed by an user key");
                }
                $reply = (boolean)$app->get_perso_id($url[2]);
                $apiResponse->output($reply, "check");
                break;

            case 'pushuserdata':
                if (count($url) < 3) {
                    $apiResponse->die("/pushuserdata/ must be followed by an user key");
                }
                $perso_id = $app->get_perso_id($url[2]) or $apiResponse->die("Invalid application user key");
                //then, falls to 'pushdata'

            case 'pushdata':
                $data_id = $_REQUEST['data'] ?: new_guid();
                //Gets data
                switch ($mode = $_REQUEST['mode']) {
                    case '':
                        $apiResponse->die("Add in your data posted or in the URL mode=file to read data from the file posted (one file per api call) or mode=request to read data from \$_REQUEST['data'].");

                    case 'request':
                        $data = $_REQUEST['data'];
                        $format = "raw";
                        break;

                    case 'file':
                        $file = $_FILES['datafile']['tmp_name'] or $apiResponse->die("File is missing");
                        if (!is_uploaded_file($file)) {
                            $apiResponse->die("Invalid form request");
                        }
                        $data = "";
                        if (preg_match('/\.tar$/', $file)) {
                            $format = "tar";
                            $data = file_get_contents($file);
                        } elseif (preg_match('/\.tar\.bz2$/', $file)) {
                            $format = "tar";
                        } elseif (preg_match('/\.bz2$/', $file)) {
                            $format = "raw";
                        } else {
                            $format = "raw";
                            $data = file_get_contents($file);
                        }
                        if ($data === "") {
                            //.bz2
                            $bz = bzopen($file, "r") or $apiResponse->die("Couldn't open $file");
                            while (!feof($bz)) {
                                $data .= bzread($bz, BUFFER_SIZE);
                            }
                            bzclose($bz);
                        }
                        unlink($file);
                        break;

                    default:
                        $apiResponse->die("Invalid mode. Expected: file, request");
                }

                //Saves data
                global $db;
                $data_id = $db->escape($data_id);
                $data = $db->escape($data);
                $perso_id = $perso_id ?: 'NULL';
                $sql = "REPLACE INTO applications_data (application_id, data_id, data_content, data_format, perso_id) VALUES ('$app->id', '$data_id', '$data', '$format', $perso_id)";
                if (!$db->query($sql)) {
                    $apiResponse->die("Can't save data");
                    message_die(SQL_ERROR, "Can't save data", '', __LINE__, __FILE__, $sql);
                }

                //Returns
                $apiResponse->output($data_id, "data");
                break;

            case 'getuserdata':
                //  /api.php/getuserdata/data_id/perso_key
                //  /api.php/getdata/data_id
                if (count($url) < 3) {
                    $apiResponse->die("/getuserdata/ must be followed by an user key");
                }
                $perso_id = $app->get_perso_id($url[2]) or $apiResponse->die("Invalid application user key");
                //then, falls to 'getdata'

            case 'getdata':
                if (count($url) < 2) {
                    $apiResponse->die('/' . $url[0] . '/ must be followed by the data ID');
                }
                if (!$perso_id) {
                    $perso_id = 'NULL';
                }
                $data_id = $db->escape($url[1]);
                $sql = "SELECT data_content FROM applications_data WHERE application_id = '$app->id' AND data_id = '$data_id' AND perso_id = $perso_id";
                if (!$result = $db->query($sql)) {
                    message_die(SQL_ERROR, "Unable to query the table", '', __LINE__, __FILE__, $sql);
                }
                while ($row = $db->fetchRow($result)) {
                }
                break;

            default:
                echo "Unknown module:";
                dprint_r($url);
                break;
        }
        break;

    default:
        echo "Unknown module:";
        dprint_r($url);
        break;
}
