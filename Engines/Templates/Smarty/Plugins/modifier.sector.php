<?php
declare(strict_types=1);

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

use Hypership\Geo\Octocube;

use Zed\Models\Geo\Location;

/**
 * Smarty sector modifier plugin
 *
 * Type:     modifier<br>
 * Name:     sector<br>
 * Purpose:  prints the sector from a location
 */
function smarty_modifier_sector (Location $location) : string {
    $xyz = explode(',', substr($location->local, 1, -1));
    $x = (int)$xyz[0];
    $y = (int)$xyz[1];
    $z = (int)$xyz[2];

    return (string)Octocube::getSector($x, $y, $z);
}
