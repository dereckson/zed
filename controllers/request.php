<?php

/**
 * Requests controller
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This controller allows the perso to send requests to the HyperShip, to a 
 * specified ship, or to a specify port requiring PTA.
 * It handle all the forms output, handling and notifications
 * for queries from users to users.
 *
 * It handles /request URL, is called from tutoriald.
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
 * @todo call this controller from HyperShip entrance pero request
 */

//
// Prepare fields
//

if (count($url) < 3) message_die(HACK_ERROR, "Expected URL: /request/code_to/code_object");
$request->to  = $url[1];
$request->obj = $url[2];

//Checks if the request template exists
if (!file_exists(sprintf("skins/%s/requests/%s.tpl", THEME, $request->obj))) {
    message_die(HACK_ERROR, "$url[2] isn't a valid request object code");
}

switch ($request->obj) {
    case "aid.reach":
        if ($request->to == "B00001")
            $request->title = "Shuttle pick up request";
        break;
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
$smarty->display("requests/$request->obj.tpl");

//Serves footer
$smarty->assign("screen", "$url[2] request");
include('footer.php');
 
?>