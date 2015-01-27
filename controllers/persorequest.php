<?php

/**
 * Persos' requests
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller handle the /requests URL.
 *
 * It can also be called with the requests SmartLine command.
 *
 * It allows to prints a content page.
 *
 * This controllers uses the persorequests.tpl view.
 *
 * This controller offer AJAX capabilities but degrades gracefully in JS.
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
 * @todo Document the request system in the API documentation
 */

///
/// Helper class and method
///

/**
 * A perso request
 */
class PersoRequest {
    public $message;
    public $requestFlag;
    public $flag;
    public $store = 'perso';
    public $value_allow = 1;
    public $value_deny  = 0;

    /**
     * Initialies a perso request
     *
     * @param string $requestFlag the request's flag
     * @param string $message the message to print
     * @param string $message the flag to set, according the request approve/denial
     */
    function __construct ($requestFlag, $message, $flag) {
        $this->requestFlag = $requestFlag;
        $this->message = $message;
        $this->flag = $flag;
    }
}

/**
 * Gets request allow URL
 *
 * @param PersoRequest $request the perso request to confirm
 * @return string the URL to allow the request
 */
function get_request_allow_url ($request) {
    return get_request_url($request->requestFlag, $request->store, $request->flag, $request->value_allow);
}

/**
 * Gets request deny URL
 *
 * @param PersoRequest $request the perso request to confirm
 * @return string the URL to deny the request
 */
function get_request_deny_url ($request) {
    return get_request_url($request->requestFlag, $request->store, $request->flag, $request->value_deny);
}

/**
 * Gets request URL
 *
 * @param string $store 'perso' or 'registry'
 * @param string $key the perso flag or registry key
 * @param string $value the value to store
 * @return the request URL
 */
function get_request_url ($requestFlag, $store, $key, $value) {
    global $Config;
    $hash = md5($_SESSION['ID'] . $Config['SecretKey'] . $requestFlag . $store . $key . $value);
    return "$Config[DoURL]/perso_request/$requestFlag/$store/$key/$value/$hash";
}

///
/// Get requests
///

//Loads perso request language file
lang_load('persorequest.conf');

//The array request will be passed to Smarty and will contain PersoRequest items.
$requests = array();

foreach ($CurrentPerso->flags as $flag => $value) {
    if ($value && substr($flag, 0, 8) == "request.") {
        if (string_starts_with($flag, 'request.api.ship.auth.')) {
            //Gets ship
            require_once('include/objects/ship.php');
            $ship_code = substr($flag, 22);
            $ship = Ship::get($ship_code);

            //Adds request
            $message = sprintf(lang_get('RequestShipAPIAuthenticate'), $ship->name);
            $requests[] = new PersoRequest($flag, $message, substr($flag, 8));
        } elseif (string_starts_with($flag, 'request.api.ship.session.')) {
            //Gets ship
            require_once('include/objects/ship.php');
            $ship_code = substr($flag, 25, 6);
            $ship = Ship::get($ship_code);

            //Adds request
            $message = sprintf(lang_get('RequestShipAPISessionConfirm'), $ship->name);
            $request = new PersoRequest($flag, $message, substr($flag, 8));
            $request->value_allow = $CurrentPerso->id;
            $request->value_deny = -1;
            $request->store = 'registry';
            $requests[] = $request;
        } else {
            message_die(GENERAL_ERROR, "Unknown request flag: $flag. Please report this bug.");
        }
    }
}

///
/// Requests handling
///

if (count($requests) == 0) {
    //If site.requests flag is at 1 but we don't have request, ignore processing
    $CurrentPerso->set_flag('site.requests', 0);

    //We don't die, so next controller takes relay
} else {
    ///
    /// HTML output
    ///

    //Serves header
    define('DOJO', true);
    $smarty->assign('PAGE_TITLE', lang_get('PersoRequests'));
    include('header.php');

    //Serves content
    $smarty->assign('requests', $requests);
    $smarty->display('persorequests.tpl');

    //Serves footer
    $smarty->assign("screen", "Perso requests");
    include('footer.php');

    //Dies
    exit;
}
