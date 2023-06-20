<?php

/**
 * Geo galaxy  class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * A 3D grid of objects
 *
 * 0.1    2010-02-08 14:02    Initial version [DcK]
 * 0.2    2010-07-25  9:20    Spherical conversion, get objects
 *
 * @package     Zed
 * @subpackage  Geo
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

namespace Zed\Models\Geo;

use Hypership\Geo\Point3D;
use Keruald\Database\DatabaseEngine;

/**
 * Geo galaxy class
 */
class Galaxy {
    /*
     * ----------------------------------------------------------------------- *
     *  Objects fetchers
     * ----------------------------------------------------------------------- *
     */

    /**
     * Gets all the coordinates of the objects in the galaxy.
     *
     * @return array An array of array. Each item is  [string object_name, string object_type, Point3D coordinates]
     */
    static function getCoordinates (DatabaseEngine $db) {
        $sql = "SELECT * FROM geo_coordinates";
        if (!$result = $db->query($sql)) {
            message_die(SQL_ERROR, "Can't query geo_coordinates view.", '', __LINE__, __FILE__, $sql);
        }

        $objects = [];
        while ($row = $db->fetchRow($result)) {
            //Demios  ship        xyz: [-50, 30, 40]
            //Kaos	  asteroid    xyz: [150, -129, 10]
            $objects[] = [$row[0], $row[1], Point3D::fromString($row[2])];
        }
        return $objects;
    }

}
