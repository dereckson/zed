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
 */

require_once('includes/content/zone.php');

//
// Helper methods
//

/**
 * Determines if a specified location is buildable
 *
 * @param GeoLocation $location the location to check
 * @param string if the location isn't buildable, a textual description of why.
 * @return bool true if the location is buildable ; otherwise, false
 *
 * @todo create a build.xml document to set what's buildable, and by who
 */
function is_buildable ($location, &$error = '') {
    //We currently allow build only in the hypership tower and core.
    if (!$location->body->hypership) {
        $error = "You can only invoke the HyperShip builder facilities inside the HyperShip.";
        return false;
    }

    //if ($build_location->place->code == "001") {
    //  //Don't allow custom builds in the corridor (T?C?)
    //}

    if ($build_location->place->code == "003") {
        message_die("Bays aren't buildable.");
        return false;
    }

    return true;
}

//
// Determines mode and initializes resources
//

switch ($build_mode = $url[1]) {
    case 'map':
        require_once('includes/geo/octocube.php');

        $build_mode     = 'map';

        //Get zones at this floor
        if ($CurrentPerso->location->global == 'B00001002') {
            $point = GeoPoint3D::fromString($CurrentPerso->location->local);
            $sector = GeoOctocube::getSectorFromPoint3D($point);
            $pattern = GeoOctocube::getRlikePatternFromSector($sector, $point->z);
            $zones = ContentZone::search($CurrentPerso->location->global, $pattern, true);
        } else {
            message_die(GENERAL_ERROR, "Can't map this area.", "Builder :: Map");
        }

        //Template
        define('DOJO', true);
        $smarty->assign('zones', $zones);
        $template = "builder_map.tpl";

        break;

    case '':
    case 'hotglue':
        //Temporary initialization code, to allow some build during the Zed alphatest
        $build_location = $CurrentPerso->location;
        $build_mode     = 'hotglue';

        $error = '';
        if (!is_buildable($build_location, $error)) {
            message_die(GENERAL_ERROR, $error, "Can't build");
        }

        //Gets or creates a new zone at build location
        $zone = ContentZone::at($build_location->global, $build_location->local, true);
        switch ($zone->type) {
            case 'hotglue':
                //All rulez
                break;

            case '':
                //New zone
                $zone->title = "Sandbox hotglue zone for $build_location->global $build_location->local";
                $zone->type = 'hotglue';
                $zone->save_to_database();
                break;

            default:
                message_die("This isn't a zone managed by hotglue.");
        }
        unset($error);

        //Template
        $smarty->assign('location', $build_location);
        $smarty->assign('zone', $zone);
        $smarty->assign('IFRAME_SRC', '/apps/hotglue/index.php?zone_' . $zone->id . '/edit');
        $template = 'builder_hotglue.tpl';
        break;

    default:
        message_die(GENERAL_ERROR, "Unknown build mode: $build_mode");
}

//
// HTML output
//
//Serves header
$smarty->assign('PAGE_TITLE', 'Builder');
include('header.php');

//Serves content
$smarty->display($template);

//Serves footer
include('footer.php');
