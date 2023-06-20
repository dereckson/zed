<?php

/**
 * Requests controller
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller allows the perso to send requests to the HyperShip,
 * to a specified ship, or to a specify port requiring PTA.
 *
 * It handles all the forms output, handling and notifications
 * for queries from users to users.
 *
 * It handles /request URL, is called from tutorial.
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
 * @todo complete requests implementation
 * @todo call this controller from Ship fly out if port is a PTA
 * @todo call this controller from HyperShip entrance perso request
 * @todo add hook to launch some events on a new request, reply or status change.
 */

//
// Prepare fields
//

use Zed\Models\Requests\Request;

if (count($url) < 3) {
    message_die(HACK_ERROR, "Expected URL: /request/code_to/code_object");
}

$request = new Request($db);

//
// Handles or print form
//
if (false) {
    //Saves the request reply
} elseif (isset($_POST['title']) || isset($_POST['message'])) {
    //Saves the request
    $request->load_from_form();
    $request->author = $CurrentPerso->id;
    $request->to = $url[1];
    $request->code = $url[2];
    $request->location_global = $CurrentPerso->location_global;
    $request->location_local = $CurrentPerso->location_local;

    $request->save_to_database();

    //Confirmation
    $template = "requests/confirm.tpl";
} else {
    $request->to  = $url[1];
    $request->code = $url[2];

    //Checks if the request template exists
    if (!file_exists(sprintf("skins/%s/requests/%s.tpl", THEME, $request->code))) {
        message_die(HACK_ERROR, "$url[2] isn't a valid request object code");
    }

    $template = "requests/$request->code.tpl";
    switch ($request->code) {
        case "aid.reach":
            if ($request->to == "B00001") {
                $request->title = "Shuttle pick up request";
            }
            break;
    }
}

//
// HTML output
//

//Serves header
define('DIJIT', true);
$smarty->assign('PAGE_TITLE', lang_get('Request'));
include('header.php');

//Serves content
$smarty->assign('request', $request);
$smarty->display($template);

//Serves footer
$smarty->assign("screen", "$url[2] request");
include('footer.php');
