-<?php

/**
 * Geo octocube class.
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
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

/**
 * Geo octocube class
 *
 * An octocube is a cube divided in 8 parts (sliced in two in x, y and z)
 *
 * The coordinates (0, 0, 0) represents the octocube center.
 */
class GeoOctocube {
    /**
     * Gets the sector from the (x, y, z) specified coordinates
     *
     * Sector will be:
     * <code>
     * //             _____ _____
     * //           /  5  /  6  /|
     * //          /- - -/- - -/ |
     * //         /_____/____ /| |
     * //        |     |     | |/|
     * //        |  7  |  8  | / | 2
     * //        |_____|_____|/| |
     * //        |     |     | |/
     * //        |  3  |  4  | /
     * //        |_____|_____|/
     * </code>
     *
     * @param int $x the x coordinate
     * @param int $y the y coordinate
     * @param int $z the z coordinate
     * @return int the number of the sector (0 if x = y = z = 0 ; otherwise, 1 to 8)
     */
    static function get_sector ($x, $y, $z) {
        //Cube center
        if ($x == 0 && $y == 0 && $z == 0) return 0;

        //One of the 8 cubes
        $sector = 1;
        if ($x >= 0) $sector++;      //we're at right
        if ($y < 0)  $sector += 2;  //we're at bottom
        if ($z >= 0) $sector += 4; //we're on the top layer

        return $sector;
    }

    /**
     * Gets the sector from the (x, y, z) specified coordinates
     * @see get_sector
     *
     * @param mixed $pt a GeoPoint3D object for the x, y, z coordinates or a parsable string
     * @return int the number of the sector (0 if x = y = z 0 ; otherwise, 1 to 8)
     */
    static function get_sector_from_point3D ($pt) {
        if (is_string($pt)) {
            $pt = GeoPoint3D::fromString($pt);
        }
        return self::get_sector($pt->x, $pt->y, $pt->z);
    }

    /**
     * Gets the base vector for the specified sector
     *
     * Example code:
     *
     * $vector = GeoOctocube::get_base_vector(4);
     * //$vector is a (1, -1, -1) array
     *
     * @param int $sector the sector number (0-8)
     * @return array if the sector is 0, (0, 0, 0) ; otherwise, an array with three signed 1 values.
     */
    static function get_base_vector ($sector) {
        switch ($sector) {
            case 0: return array(0, 0, 0);
            case 1: return array(-1, 1, -1);
            case 2: return array(1, 1, -1);
            case 3: return array(-1, -1, -1);
            case 4: return array(1, -1, -1);
            case 5: return array(-1, 1, 1);
            case 6: return array(1, 1, 1);
            case 7: return array(-1, -1, 1);
            case 8: return array(1, -1, 1);
            default: message_die(GENERAL_ERROR, "Invalid sector: $sector", "GeoOctocube::get_base_vector");
        }
    }


    /**
     * Gets SQL RLIKE pattern for the specified sector
     *
     * @param int $sector the sector number (0-8)
     * @param int $z if not null, limits the query to the specified z coordinate [optional]
     * @return string a SQL clause like "([0-9]+, -[0,9]+, [0,9]+)"
     */
    static function get_rlike_pattern_from_sector ($sector, $z = null) {
        if ($sector == 0) return "(0, 0, 0)";

        $vector = self::get_base_vector($sector);

        //x
        if ($vector[0] == 1)
            $query = "([0-9]+, ";
        else
            $query = "(-[0-9]+, ";

        //y
        if ($vector[1] == 1)
            $query .= "[0-9]+, ";
        else
            $query .= "-[0-9]+, ";

        //z
        if ($z !== null) {
            $query .= "$z)";
        } elseif ($vector[2] == "1") {
            $query .= "[0-9]+)";
        } else {
            $query .= "-[0-9]+)";
        }

        return $query;
    }
}

?>
