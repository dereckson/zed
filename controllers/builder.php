<?php

/**
 * Builder
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * @package     Zed
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/gpl-2.0.php GPLv2
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo create a build.xml document to set what's buildable, and by who
 */

//
// Temporary initialization code, to allow some build during the Zed alphatest
//

$build_location = $CurrentPerso->location;
$build_mode     = 'hotglue';
if (!$build_location->body->hypership) {
    message_die("You can only invoke the HyperShip builder facilities inside the HyperShip.");
}

if ($build_location->place->code == "003") {
    message_die("Bays aren't buildable.");
}

//
// Prepare zone
//

//TODO: get local zone
include('includes/content/zone.php');
if (true) {
    //Gets or creates a new zone at build location
    $zone = ContentZone::at($build_location->global, $build_location->local, true);
    $zone->title = "Sandbox hotglue zone for $build_location->global $build_location->local";
    $zone->type = 'hotglue';
    $zone->save_to_database();
}

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', 'Builder');
include('header.php');

//Hotglue iframe
$smarty->assign('location', $build_location);
$smarty->assign('zone', $zone);
switch ($build_mode) {
    case 'hotglue':
        $smarty->assign('IFRAME_SRC', '/apps/hotglue/?zone_' . $zone->id . '/edit');
        $smarty->display('builder_hotglue.tpl');
        break;

    default:
        message_die(GENERAL_ERROR, "Unknown build mode: $build_mode");
        break;
}

//Servers footer
include('footer.php');

?>
